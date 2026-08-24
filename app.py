"""Fax235 — plateforme d'entraide étudiante (Streamlit)."""

import base64

import streamlit as st

import db
import storage

st.set_page_config(page_title="Fax235", layout="wide")
db.init_db()

st.markdown(
    """
    <style>
    .stButton > button, .stFormSubmitButton > button, .stDownloadButton > button {
        transition: transform 0.12s ease, box-shadow 0.12s ease, background-color 0.12s ease;
        background-color: #7A1F2B;
        color: #FFFFFF;
        border: 1px solid #7A1F2B;
    }
    .stButton > button:hover, .stFormSubmitButton > button:hover, .stDownloadButton > button:hover {
        background-color: #5E1721;
        border-color: #5E1721;
        color: #FFFFFF;
    }
    .stButton > button:active, .stFormSubmitButton > button:active, .stDownloadButton > button:active {
        transform: scale(0.97);
    }
    div[class*="st-key-menu_toggle"] button,
    div[class*="st-key-avatar_trigger"] button {
        background-color: transparent !important;
        border: none !important;
    }
    div[data-testid="stPopoverBody"] .stButton > button {
        background-color: #FFFFFF !important;
        border: 1px solid #E6DFDA !important;
    }
    div[data-testid="stPopoverBody"] .stButton > button:hover {
        background-color: #F5F1EE !important;
        border-color: #7A1F2B !important;
    }
    div[class*="st-key-site_header"], div[class*="st-key-site_footer"] {
        background-color: #F5F1EE;
        position: fixed;
        left: 0;
        right: 0;
        z-index: 998;
    }
    div[class*="st-key-site_header"] {
        top: 60px;
        min-height: 176px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0.75rem 2rem;
    }
    div[class*="st-key-header_nav_row"] {
        width: 100%;
    }
    .marquee-track {
        overflow: hidden;
        user-select: none;
        margin-bottom: 0.6rem;
        -webkit-mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
        mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
    }
    .marquee-content {
        display: flex;
        width: max-content;
        gap: 0.75rem;
        animation: marquee-scroll 34s linear infinite;
        padding: 0.25rem 0.25rem;
    }
    .marquee-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        background: #FFFFFF;
        border: 1px solid #E6DFDA;
        border-radius: 999px;
        padding: 0.4rem 1rem;
        font-weight: 500;
        color: #221D1D;
        font-size: 0.88rem;
    }
    .marquee-icon {
        font-size: 1rem;
    }
    @keyframes marquee-scroll {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }
    @media (prefers-reduced-motion: reduce) {
        .marquee-content { animation: none; }
    }
    div[class*="st-key-site_header"] .stButton > button {
        white-space: nowrap;
    }
    div[class*="st-key-site_header"] [data-testid="stPageLink"] p {
        white-space: nowrap;
    }
    div[class*="st-key-header_left"] [data-testid="stPageLink"] {
        background-color: #7A1F2B;
        border-radius: 999px;
        padding: 0.45rem 1.1rem;
        transition: background-color 0.12s ease;
    }
    div[class*="st-key-header_left"] [data-testid="stPageLink"] p {
        color: #FFFFFF !important;
    }
    div[class*="st-key-header_left"] [data-testid="stPageLink"]:hover {
        background-color: #5E1721;
    }
    div[class*="st-key-header_left"] {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }
    div[class*="st-key-header_left"] [data-testid="stHorizontalBlock"] {
        justify-content: flex-start !important;
        gap: 1.2rem !important;
    }
    div[class*="st-key-header_left"] [data-testid="stColumn"] {
        width: auto !important;
        flex: 0 0 auto !important;
        min-width: 0 !important;
    }
    div[class*="st-key-menu_toggle"] button {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        font-size: 2.2rem;
        line-height: 1;
        padding: 0 !important;
        min-height: auto !important;
        color: #221D1D;
    }
    div[class*="st-key-menu_toggle"] button p {
        font-size: 2.2rem;
        line-height: 1;
    }
    div[class*="st-key-menu_toggle"] button:hover {
        color: #7A1F2B;
        background: transparent !important;
    }
    div[class*="st-key-header_right"] {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        width: 100%;
        gap: 0.4rem;
    }
    div[class*="st-key-header_right"] [data-testid="stHorizontalBlock"] {
        justify-content: flex-end !important;
        gap: 0.6rem !important;
    }
    div[class*="st-key-header_right"] [data-testid="stColumn"] {
        width: auto !important;
        flex: 0 0 auto !important;
        min-width: 0 !important;
    }
    div[class*="st-key-header_right"] img {
        border-radius: 50%;
        object-fit: cover;
        aspect-ratio: 1 / 1;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
    }
    div[class*="st-key-avatar_trigger"] button {
        border: none;
        padding: 0;
        min-height: 36px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
    }
    div[class*="st-key-avatar_trigger"] button:hover {
        box-shadow: 0 0 0 2px #7A1F2B;
    }
    div[class*="st-key-avatar_trigger"] button p {
        display: none;
    }
    [data-testid="stPopover"] button p {
        white-space: nowrap;
    }
    div[data-testid="stPopoverBody"] {
        width: 460px !important;
        max-width: 92vw !important;
        max-height: 420px !important;
        overflow-y: auto !important;
        padding: 0.75rem !important;
    }
    div[data-testid="stPopoverBody"] .stButton > button {
        font-size: 1.3rem !important;
        padding: 0.35rem 0 !important;
        min-height: 2.4rem !important;
    }
    div[class*="st-key-reply_actions_row"] [data-testid="stHorizontalBlock"],
    div[class*="st-key-comment_actions_row"] [data-testid="stHorizontalBlock"] {
        justify-content: flex-start !important;
        gap: 0.5rem !important;
    }
    div[class*="st-key-reply_actions_row"] [data-testid="stColumn"],
    div[class*="st-key-comment_actions_row"] [data-testid="stColumn"] {
        width: auto !important;
        flex: 0 0 auto !important;
        min-width: 0 !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stFileUploaderDropzoneInstructions"],
    div[class*="st-key-comment_upload_compact"] [data-testid="stFileUploaderDropzoneInstructions"],
    div[class*="st-key-reply_upload_compact"] [data-testid="stWidgetLabel"],
    div[class*="st-key-comment_upload_compact"] [data-testid="stWidgetLabel"] {
        display: none !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stFileUploaderDropzone"],
    div[class*="st-key-comment_upload_compact"] [data-testid="stFileUploaderDropzone"] {
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        min-height: 0 !important;
        justify-content: flex-start !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stBaseButton-secondary"] [data-testid="stMarkdownContainer"] p,
    div[class*="st-key-comment_upload_compact"] [data-testid="stBaseButton-secondary"] [data-testid="stMarkdownContainer"] p {
        font-size: 0 !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stBaseButton-secondary"] [data-testid="stMarkdownContainer"] p::after,
    div[class*="st-key-comment_upload_compact"] [data-testid="stBaseButton-secondary"] [data-testid="stMarkdownContainer"] p::after {
        content: "Upload un fichier";
        font-size: 1rem;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stBaseButton-secondary"],
    div[class*="st-key-comment_upload_compact"] [data-testid="stBaseButton-secondary"] {
        background-color: #7A1F2B !important;
        color: #FFFFFF !important;
        border: 1px solid #7A1F2B !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stBaseButton-secondary"] span[data-testid="stIconMaterial"],
    div[class*="st-key-comment_upload_compact"] [data-testid="stBaseButton-secondary"] span[data-testid="stIconMaterial"] {
        color: #FFFFFF !important;
    }
    div[class*="st-key-reply_upload_compact"] [data-testid="stBaseButton-secondary"]:hover,
    div[class*="st-key-comment_upload_compact"] [data-testid="stBaseButton-secondary"]:hover {
        background-color: #5E1721 !important;
        border-color: #5E1721 !important;
    }
    div[class*="st-key-site_footer"] {
        bottom: 0;
        min-height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    div[class*="st-key-site_footer"] p {
        text-align: center;
        width: 100%;
    }
    div[class*="st-key-auth_card"] {
        min-height: 15vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-top: 2rem;
    }
    div[class*="st-key-auth_card"] > div {
        width: 100%;
    }
    div[class*="st-key-login_links_row"] [data-testid="stPageLink"] {
        background-color: #FFFFFF;
        border: 1px solid #7A1F2B;
        border-radius: 8px;
        padding: 0.5rem 0.6rem;
        width: 100%;
        transition: background-color 0.12s ease;
    }
    div[class*="st-key-login_links_row"] a[data-testid="stPageLink-NavLink"] {
        justify-content: center !important;
    }
    div[class*="st-key-login_links_row"] [data-testid="stPageLink"] p {
        color: #7A1F2B !important;
        white-space: normal !important;
        text-align: center;
        font-size: 0.85rem;
    }
    div[class*="st-key-login_links_row"] [data-testid="stPageLink"]:hover {
        background-color: #F5F1EE;
    }
    </style>
    """,
    unsafe_allow_html=True,
)

MARQUEE_ITEMS = [
    ("📐", "Mathématiques"),
    ("⚛️", "Physique"),
    ("🧪", "Chimie"),
    ("🧬", "SVT"),
    ("💻", "Informatique"),
    ("⚖️", "Droit"),
    ("📖", "Lettres Modernes"),
    ("🌍", "Anglais"),
    ("📊", "Économie"),
    ("🏛️", "Histoire"),
    ("🗺️", "Géographie"),
    ("⛰️", "Géologie"),
    ("📡", "Télécommunication"),
    ("💼", "Technique Commerciale"),
    ("🗂️", "Gestion"),
    ("👥", "Sociologie"),
    ("🎯", "Révisez avec les sujets d'anciens étudiants"),
]


def render_marquee():
    items_html = "".join(
        f'<div class="marquee-item"><span class="marquee-icon">{icon}</span><span>{label}</span></div>'
        for icon, label in MARQUEE_ITEMS
    )
    st.markdown(
        f"""
        <div class="marquee-track">
            <div class="marquee-content">
                {items_html}
                {items_html}
            </div>
        </div>
        """,
        unsafe_allow_html=True,
    )

if "user" not in st.session_state:
    st.session_state.user = None

if st.session_state.user is None:
    token = st.query_params.get("token")
    if token:
        session_row = db.get_connection().execute(
            "SELECT u.* FROM sessions s JOIN users u ON u.id = s.user_id WHERE s.token = ?",
            (token,),
        ).fetchone()
        if session_row:
            st.session_state.user = dict(session_row)

login_page = st.Page("views/login.py", title="Connexion")

if st.session_state.user is None:
    # Pas de connexion : aucun header, footer ni sidebar — uniquement la page en cours.
    pages = [
        login_page,
        st.Page("views/register.py", title="Inscription"),
        st.Page("views/reset_password.py", title="Mot de passe oublié"),
        st.Page("views/sujets.py", title="Sujets & corrections"),
        st.Page("views/recherche.py", title="Recherche"),
    ]
    pg = st.navigation(pages, position="hidden")
    if st.session_state.pop("force_login_redirect", False):
        st.switch_page(login_page)
    pg.run()
else:
    pages = [
        st.Page("views/sujets.py", title="Sujets & corrections", default=True),
        st.Page("views/recherche.py", title="Recherche"),
        st.Page("views/publier.py", title="Envoyer un sujet/correction"),
    ]
    if st.session_state.user["role"] == "admin":
        pages.append(st.Page("views/admin.py", title="Administration"))

    pg = st.navigation(pages, position="hidden")

    st.markdown(
        """
        <style>
        [data-testid="stHeader"] {
            background-color: #F5F1EE;
        }
        section[data-testid="stMain"] .stMainBlockContainer {
            padding-top: 13rem;
            padding-bottom: 5rem;
        }
        @media (max-width: 640px) {
            div[class*="st-key-site_header"], div[class*="st-key-site_footer"] {
                position: static !important;
                min-height: 0 !important;
            }
            section[data-testid="stMain"] .stMainBlockContainer {
                padding-top: 1.5rem !important;
                padding-bottom: 2rem !important;
            }
            div[class*="st-key-header_left"] [data-testid="stHorizontalBlock"],
            div[class*="st-key-header_right"] [data-testid="stHorizontalBlock"] {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                row-gap: 0.5rem !important;
            }
            div[class*="st-key-header_right"] [data-testid="stColumn"] {
                width: auto !important;
                max-width: max-content !important;
                flex: 0 1 auto !important;
            }
            div[class*="st-key-header_left"] [data-testid="stHorizontalBlock"] > [data-testid="stColumn"]:nth-child(1) {
                width: auto !important;
                max-width: max-content !important;
                flex: 0 0 auto !important;
            }
            div[class*="st-key-header_left"] [data-testid="stHorizontalBlock"] > [data-testid="stColumn"]:nth-child(n+2) {
                flex: 1 1 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            div[class*="st-key-header_left"] [data-testid="stPageLink"] {
                padding: 0.4rem 0.75rem !important;
                justify-content: center !important;
                min-height: 0 !important;
            }
            div[class*="st-key-header_left"] [data-testid="stPageLink"] p {
                white-space: nowrap !important;
                text-align: center;
                font-size: 0.8rem !important;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
        </style>
        """,
        unsafe_allow_html=True,
    )

    with st.container(key="site_header"):
        render_marquee()

        n_nav = len(pages)

        nav_row = st.container(key="header_nav_row")
        left_area, right_area = nav_row.columns([2.7 + 1.9 * n_nav, 5], vertical_alignment="center")

        if "nav_expanded" not in st.session_state:
            st.session_state.nav_expanded = True

        with left_area:
            with st.container(key="header_left"):
                n_nav_visible = n_nav if st.session_state.nav_expanded else 0
                left_widths = [2.2] + [1.9] * n_nav_visible
                left_cols = st.columns(left_widths, vertical_alignment="center")

                with left_cols[0]:
                    with st.container(key="menu_toggle"):
                        if st.button("☰", key="menu_toggle_btn"):
                            st.session_state.nav_expanded = not st.session_state.nav_expanded
                            st.rerun()

                if st.session_state.nav_expanded:
                    for i, page in enumerate(pages):
                        with left_cols[1 + i]:
                            st.page_link(page)

        u = st.session_state.user
        if u.get("photo") and str(u["photo"]).startswith("http"):
            avatar_src = u["photo"]
        else:
            default_avatar = db.BASE_DIR / "assets" / "default_avatar.png"
            avatar_src = f"data:image/png;base64,{base64.b64encode(default_avatar.read_bytes()).decode()}"

        @st.dialog("Photo de profil")
        def show_avatar_dialog():
            st.image(avatar_src, width=280)
            st.divider()
            st.caption("Changer ma photo de profil")
            new_photo = st.file_uploader(
                "Nouvelle photo", type=["jpg", "jpeg", "png"], key="avatar_uploader", label_visibility="collapsed"
            )
            if new_photo is not None:
                if st.button("Valider cette photo", use_container_width=True):
                    with st.spinner("Envoi de la photo..."):
                        photo_url = storage.upload_file(new_photo.getvalue(), new_photo.name)
                    conn = db.get_connection()
                    conn.execute("UPDATE users SET photo = ? WHERE id = ?", (photo_url, u["id"]))
                    conn.commit()
                    st.session_state.user["photo"] = photo_url
                    st.rerun()

        with right_area:
            with st.container(key="header_right"):
                with st.container(key="header_right_top"):
                    av_col, name_col = st.columns([1, 3], gap="small", vertical_alignment="center")
                    with av_col:
                        with st.container(key="avatar_trigger"):
                            if st.button("Photo de profil"):
                                show_avatar_dialog()

                        st.markdown(
                            f"""
                            <style>
                            div[class*="st-key-avatar_trigger"] button {{
                                background-image: url('{avatar_src}');
                            }}
                            </style>
                            """,
                            unsafe_allow_html=True,
                        )
                    with name_col:
                        st.write(f"**{u['firstname']} {u['lastname']}**")

                if st.button("Se déconnecter"):
                    token = st.query_params.get("token")
                    if token:
                        db.get_connection().execute("DELETE FROM sessions WHERE token = ?", (token,))
                        db.get_connection().commit()
                    st.query_params.clear()
                    st.session_state.user = None
                    st.session_state.force_login_redirect = True
                    st.rerun()
    st.divider()

    pg.run()

    st.divider()
    with st.container(key="site_footer"):
        st.caption("© 2026 Fax235 · Au service de tous")
