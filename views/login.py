"""Connexion à l'espace étudiant."""

import secrets

import streamlit as st

import auth
from db import get_connection

with st.container(key="auth_card"):
    col_left, col_center, col_right = st.columns([1, 1.3, 1])
    with col_center:
        st.title("Connexion")

        if st.session_state.pop("just_registered", False):
            st.success("Inscription réussie ! Vous pouvez maintenant vous connecter.")

        if st.session_state.pop("password_was_reset", False):
            st.success("Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.")

        with st.form("login_form"):
            matricule = st.text_input("Matricule")
            password = st.text_input("Mot de passe", type="password")
            submitted = st.form_submit_button("Se connecter", use_container_width=True)

        if submitted:
            conn = get_connection()
            user = conn.execute(
                "SELECT * FROM users WHERE matricule = ?", (matricule.strip().upper(),)
            ).fetchone()
            if user and auth.verify_password(password, user["password_hash"]):
                token = secrets.token_urlsafe(32)
                conn.execute("INSERT INTO sessions (token, user_id) VALUES (?, ?)", (token, user["id"]))
                conn.commit()
                st.session_state.user = dict(user)
                st.session_state.session_token = token
                st.query_params["token"] = token
                st.rerun()
            else:
                st.error("Matricule ou mot de passe incorrect.")

        with st.container(key="login_links_row"):
            link_col1, link_col2 = st.columns(2)
            with link_col1:
                st.page_link("views/register.py", label="Inscrivez-vous", use_container_width=True)
            with link_col2:
                st.page_link("views/reset_password.py", label="Mot de passe oublié ?", use_container_width=True)
