"""Réinitialisation du mot de passe par vérification d'identité (sans e-mail)."""

import streamlit as st

import auth
from db import get_connection

with st.container(key="auth_card"):
    col_left, col_center, col_right = st.columns([1, 1.3, 1])
    with col_center:
        st.title("Mot de passe oublié")
        st.caption("Confirmez votre identité pour définir un nouveau mot de passe.")

        with st.form("reset_password_form"):
            matricule = st.text_input("Matricule")
            firstname = st.text_input("Prénom")
            lastname = st.text_input("Nom")
            universite = st.text_input("Université")
            new_password = st.text_input("Nouveau mot de passe", type="password")
            confirm = st.text_input("Confirmer le nouveau mot de passe", type="password")
            submitted = st.form_submit_button("Réinitialiser le mot de passe", use_container_width=True)

        if submitted:
            errors = []
            if len(new_password) < 6:
                errors.append("Le nouveau mot de passe doit contenir au moins 6 caractères.")
            if new_password != confirm:
                errors.append("Les mots de passe ne correspondent pas.")

            if errors:
                for e in errors:
                    st.error(e)
            else:
                conn = get_connection()
                user = conn.execute(
                    "SELECT * FROM users WHERE matricule = ?",
                    (matricule.strip().upper(),),
                ).fetchone()

                identity_matches = (
                    user is not None
                    and user["firstname"].strip().lower() == firstname.strip().lower()
                    and user["lastname"].strip().lower() == lastname.strip().lower()
                    and user["universite"].strip().lower() == universite.strip().lower()
                )

                if not identity_matches:
                    st.error("Aucun compte ne correspond à ces informations.")
                else:
                    conn.execute(
                        "UPDATE users SET password_hash = ? WHERE id = ?",
                        (auth.hash_password(new_password), user["id"]),
                    )
                    conn.commit()
                    st.session_state.password_was_reset = True
                    st.switch_page("views/login.py")

        st.page_link("views/login.py", label="Retour à la connexion")
