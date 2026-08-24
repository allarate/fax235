"""Dépôt d'un sujet ou d'une correction par un étudiant connecté."""

import time

import streamlit as st

import storage
from db import get_connection

if not st.session_state.user:
    attempts = st.session_state.get("_publier_guard_attempts", 0)
    if attempts < 8:
        st.session_state["_publier_guard_attempts"] = attempts + 1
        st.info("Chargement...")
        time.sleep(0.4)
        st.rerun()
    st.warning("Vous devez être connecté pour publier un document.")
    st.stop()
else:
    st.session_state.pop("_publier_guard_attempts", None)

conn = get_connection()

col_left, col_mid, col_right = st.columns([1, 2, 1])

with col_mid:
    st.title("Envoyer un sujet/correction")
    st.caption("Votre document sera visible immédiatement dans la recherche.")

    with st.form("publier_form", clear_on_submit=True):
        filiere = st.text_input("Filière")
        matiere = st.text_input("Matière")
        annee = st.number_input("Année", min_value=2000, max_value=2099, value=2024, step=1)
        type_doc = st.selectbox("Type", ["sujet", "corrige"], format_func=lambda x: "Sujet" if x == "sujet" else "Corrigé")
        fichier = st.file_uploader("Fichier (PDF, JPG ou PNG — 10 Mo max)", type=["pdf", "jpg", "jpeg", "png"])

        submitted = st.form_submit_button("Soumettre", use_container_width=True)

        if submitted:
            errors = []
            if len(filiere.strip()) < 2:
                errors.append("La filière est obligatoire.")
            if not matiere.strip():
                errors.append("La matière est obligatoire.")
            if fichier is None:
                errors.append("Un fichier est requis.")
            elif fichier.size > 10 * 1024 * 1024:
                errors.append("Le fichier dépasse la taille maximale de 10 Mo.")

            if errors:
                for e in errors:
                    st.error(e)
            else:
                with st.spinner("Envoi du fichier..."):
                    file_url = storage.upload_file(fichier.getvalue(), fichier.name)

                conn.execute(
                    "INSERT INTO sujets (filiere, matiere, type, annee, fichier, statut, user_id) "
                    "VALUES (?, ?, ?, ?, ?, 'valide', ?)",
                    (filiere.strip(), matiere.strip(), type_doc, int(annee), file_url, st.session_state.user["id"]),
                )
                conn.commit()
                st.success("Document publié avec succès. Il est désormais visible dans la recherche.")

    st.divider()
    st.subheader("Mes documents soumis")

    mine = conn.execute(
        "SELECT * FROM sujets WHERE user_id = ? ORDER BY created_at DESC",
        (st.session_state.user["id"],),
    ).fetchall()

    if not mine:
        st.caption("Vous n'avez encore soumis aucun document.")
    else:
        for s in mine:
            badge = "Corrigé" if s["type"] == "corrige" else "Sujet"
            st.write(f"**{s['matiere']}** — {s['filiere']} · {badge} · {s['annee']}")
