"""Page d'accueil : accès à la publication et à la recherche de sujets/corrections."""

import streamlit as st

st.title("Sujets & corrections")
st.caption("Consultez les anciens sujets et corrigés déposés par les étudiants.")

st.divider()

col1, col2 = st.columns(2)

with col1:
    with st.container(border=True):
        if st.session_state.user:
            st.page_link("views/publier.py", label="Publier un sujet ou une correction", use_container_width=True)
        else:
            st.page_link("views/login.py", label="Connectez-vous pour publier un document", use_container_width=True)

with col2:
    with st.container(border=True):
        st.page_link("views/recherche.py", label="Rechercher un document", use_container_width=True)
