"""Inscription d'un nouvel étudiant."""

import streamlit as st

import auth
from db import get_connection

with st.container(key="auth_card"):
    col_left, col_center, col_right = st.columns([1, 1.3, 1])
    with col_center:
        st.title("Inscription")

        with st.form("register_form"):
            col1, col2 = st.columns(2)
            with col1:
                firstname = st.text_input("Prénom")
            with col2:
                lastname = st.text_input("Nom")
            universite = st.text_input("Université")
            matricule = st.text_input("Matricule")
            password = st.text_input("Mot de passe", type="password")
            confirm = st.text_input("Confirmer le mot de passe", type="password")
            submitted = st.form_submit_button("S'inscrire", use_container_width=True)

            if submitted:
                errors = []
                if len(firstname.strip()) < 2:
                    errors.append("Le prénom doit contenir au moins 2 caractères.")
                if len(lastname.strip()) < 2:
                    errors.append("Le nom doit contenir au moins 2 caractères.")
                if len(universite.strip()) < 2:
                    errors.append("L'université est obligatoire.")
                if len(matricule.strip()) < 2:
                    errors.append("Le matricule est obligatoire.")
                if len(password) < 6:
                    errors.append("Le mot de passe doit contenir au moins 6 caractères.")
                if password != confirm:
                    errors.append("Les mots de passe ne correspondent pas.")

                matricule_normalise = matricule.strip().upper()

                conn = get_connection()
                if not errors and conn.execute(
                    "SELECT id FROM users WHERE matricule = ?", (matricule_normalise,)
                ).fetchone():
                    errors.append("Ce matricule est déjà utilisé.")

                if errors:
                    for e in errors:
                        st.error(e)
                else:
                    conn.execute(
                        "INSERT INTO users (firstname, lastname, universite, matricule, password_hash, role) "
                        "VALUES (?, ?, ?, ?, ?, 'etudiant')",
                        (
                            firstname.strip(),
                            lastname.strip(),
                            universite.strip(),
                            matricule_normalise,
                            auth.hash_password(password),
                        ),
                    )
                    conn.commit()
                    st.session_state.just_registered = True
                    st.switch_page("views/login.py")

        st.page_link("views/login.py", label="Déjà inscrit ? Connectez-vous")
