"""Espace administrateur : modération des documents et gestion des utilisateurs."""

import time

import streamlit as st

from db import get_connection

if not st.session_state.user:
    attempts = st.session_state.get("_admin_guard_attempts", 0)
    if attempts < 8:
        st.session_state["_admin_guard_attempts"] = attempts + 1
        st.info("Chargement...")
        time.sleep(0.4)
        st.rerun()
    st.error("Accès réservé aux administrateurs.")
    st.stop()
else:
    st.session_state.pop("_admin_guard_attempts", None)

if st.session_state.user["role"] != "admin":
    st.error("Accès réservé aux administrateurs.")
    st.stop()

st.title("Administration")

conn = get_connection()

tab_sujets, tab_users = st.tabs(["Documents publiés", "Utilisateurs"])

with tab_sujets:
    sujets = conn.execute(
        "SELECT s.*, u.firstname, u.lastname FROM sujets s "
        "JOIN users u ON u.id = s.user_id "
        "ORDER BY s.created_at DESC"
    ).fetchall()

    if not sujets:
        st.info("Aucun document publié pour le moment.")

    for s in sujets:
        with st.container(border=True):
            badge = "Corrigé" if s["type"] == "corrige" else "Sujet"
            st.subheader(f"{s['matiere']}")
            st.write(f"{badge} · {s['filiere']} · {s['annee']}")
            st.caption(f"Déposé par {s['firstname']} {s['lastname']}")

            if st.button("Retirer ce document", key=f"del_sujet_{s['id']}"):
                conn.execute("DELETE FROM sujets WHERE id = ?", (s["id"],))
                conn.commit()
                st.rerun()

with tab_users:
    users = conn.execute(
        "SELECT id, firstname, lastname, matricule, universite, role "
        "FROM users ORDER BY created_at DESC"
    ).fetchall()
    for u in users:
        col1, col2 = st.columns([4, 1])
        with col1:
            st.write(f"**{u['firstname']} {u['lastname']}** — Matricule {u['matricule']} ({u['role']})")
            st.caption(f"{u['universite']}")
        with col2:
            if u["role"] != "admin" and u["id"] != st.session_state.user["id"]:
                if st.button("Promouvoir admin", key=f"promote_{u['id']}"):
                    conn.execute("UPDATE users SET role = 'admin' WHERE id = ?", (u["id"],))
                    conn.commit()
                    st.rerun()
