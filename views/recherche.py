"""Recherche et consultation des sujets/corrections publiés, avec réactions et commentaires imbriqués."""

import base64
import difflib
import unicodedata

import streamlit as st

import storage
from db import UPLOAD_DIR, get_connection

IMAGE_EXTENSIONS = {"jpg", "jpeg", "png"}
REACTION_EMOJIS = [
    "😀", "😃", "😄", "😁", "😆", "😅", "😂", "🤣",
    "😊", "😇", "🙂", "😉", "😍", "🥰", "😘", "😜",
    "🤔", "🤨", "😎", "🥳", "😏", "😴", "🥱", "😢",
    "😭", "😤", "😡", "🤯", "😳", "🥵", "🥶", "😱",
    "👍", "👎", "👏", "🙌", "🙏", "✌️", "🤞", "👌",
    "💪", "🤝", "👋", "❤️", "🧡", "💛", "💚", "💙",
    "💜", "🖤", "🤍", "💔", "💕", "💯", "🔥", "✨",
    "🎉", "🎊", "⭐", "⚡", "👀", "🏆", "📚", "✅",
]
EMOJI_GRID_COLS = 8


def normalize(text: str) -> str:
    text = text.lower().strip()
    text = unicodedata.normalize("NFKD", text)
    return "".join(c for c in text if not unicodedata.combining(c))


def word_matches(query_word: str, target_words: list[str], cutoff: float = 0.72) -> bool:
    for tw in target_words:
        if not tw:
            continue
        if query_word in tw or tw in query_word:
            return True
        if difflib.SequenceMatcher(None, query_word, tw).ratio() >= cutoff:
            return True
    return False


def matches_search(query: str, *fields: str) -> bool:
    query_words = [w for w in normalize(query).split() if w]
    if not query_words:
        return True
    target_words = normalize(" ".join(fields)).split()
    return all(word_matches(qw, target_words) for qw in query_words)


def reactions_summary(conn, target_type: str, target_id: int) -> dict[str, int]:
    rows = conn.execute(
        "SELECT emoji, COUNT(*) AS cnt FROM reactions WHERE target_type = ? AND target_id = ? GROUP BY emoji",
        (target_type, target_id),
    ).fetchall()
    return {r["emoji"]: r["cnt"] for r in rows}


def toggle_reaction(conn, target_type: str, target_id: int, user_id: int, emoji: str) -> None:
    existing = conn.execute(
        "SELECT id FROM reactions WHERE target_type = ? AND target_id = ? AND user_id = ? AND emoji = ?",
        (target_type, target_id, user_id, emoji),
    ).fetchone()
    if existing:
        conn.execute("DELETE FROM reactions WHERE id = ?", (existing["id"],))
    else:
        conn.execute(
            "INSERT INTO reactions (target_type, target_id, user_id, emoji) VALUES (?, ?, ?, ?)",
            (target_type, target_id, user_id, emoji),
        )
    conn.commit()


def render_reactions(conn, target_type: str, target_id: int, key_prefix: str) -> None:
    summary = reactions_summary(conn, target_type, target_id)
    col_pick, col_summary = st.columns([1, 6], vertical_alignment="center")
    with col_pick:
        if st.session_state.user:
            with st.popover("Réagir"):
                for row_start in range(0, len(REACTION_EMOJIS), EMOJI_GRID_COLS):
                    row_emojis = REACTION_EMOJIS[row_start : row_start + EMOJI_GRID_COLS]
                    emoji_cols = st.columns(EMOJI_GRID_COLS)
                    for col, emoji in zip(emoji_cols, row_emojis):
                        with col:
                            if st.button(emoji, key=f"{key_prefix}_pick_{emoji}"):
                                toggle_reaction(conn, target_type, target_id, st.session_state.user["id"], emoji)
                                st.rerun()
    with col_summary:
        if summary:
            st.caption("  ".join(f"{emoji} {count}" for emoji, count in summary.items()))


def render_comment(conn, comment, by_parent: dict, sujet_id: int, depth: int = 0) -> None:
    block_key = f"comment_block_{comment['id']}"
    if depth > 0:
        st.markdown(
            f"""
            <style>
            div[class*="st-key-{block_key}"] {{
                margin-left: {min(depth, 6) * 28}px;
                border-left: 2px solid #E6DFDA;
                padding-left: 12px;
            }}
            </style>
            """,
            unsafe_allow_html=True,
        )

    with st.container(key=block_key):
        st.markdown(f"**{comment['firstname']} {comment['lastname']}**")
        if comment["message"]:
            st.write(comment["message"])

        if comment["fichier"]:
            file_ref = comment["fichier"]
            ext = storage.extension_of(file_ref)
            if ext in IMAGE_EXTENSIONS:
                st.image(file_ref, width=200)
            else:
                file_bytes = storage.fetch_bytes(file_ref)
                download_name = file_ref.split("?", 1)[0].rsplit("/", 1)[-1]
                st.download_button(
                    "Télécharger la pièce jointe",
                    file_bytes,
                    file_name=download_name,
                    key=f"cmt_dl_{comment['id']}",
                )

        render_reactions(conn, "commentaire", comment["id"], key_prefix=f"cmt_{comment['id']}")

        reply_open_key = f"reply_open_{comment['id']}"
        reply_file_gen_key = f"reply_file_gen_{comment['id']}"
        if reply_file_gen_key not in st.session_state:
            st.session_state[reply_file_gen_key] = 0

        if st.session_state.user:
            with st.container(key=f"reply_actions_row_{comment['id']}"):
                reply_btn_col, reply_upload_col = st.columns([1, 1], gap="small")
                with reply_btn_col:
                    if st.button("Répondre", key=f"reply_btn_{comment['id']}"):
                        st.session_state[reply_open_key] = not st.session_state.get(reply_open_key, False)
                with reply_upload_col:
                    with st.container(key=f"reply_upload_compact_{comment['id']}"):
                        quick_file = st.file_uploader(
                            "Joindre un fichier",
                            type=["pdf", "jpg", "jpeg", "png"],
                            key=f"reply_quick_file_{comment['id']}_{st.session_state[reply_file_gen_key]}",
                            label_visibility="collapsed",
                        )
                if quick_file is not None:
                    with st.spinner("Envoi du fichier..."):
                        file_url = storage.upload_file(quick_file.getvalue(), quick_file.name)
                    conn.execute(
                        "INSERT INTO commentaires (sujet_id, user_id, message, parent_id, fichier) VALUES (?, ?, ?, ?, ?)",
                        (sujet_id, st.session_state.user["id"], "", comment["id"], file_url),
                    )
                    conn.commit()
                    st.session_state[reply_file_gen_key] += 1
                    st.rerun()

        if st.session_state.get(reply_open_key, False):
            with st.form(key=f"reply_form_{comment['id']}", clear_on_submit=True):
                reply_msg = st.text_area(
                    "Réponse", key=f"reply_msg_{comment['id']}", label_visibility="collapsed", placeholder="Votre réponse..."
                )
                if st.form_submit_button("Envoyer"):
                    if reply_msg.strip():
                        conn.execute(
                            "INSERT INTO commentaires (sujet_id, user_id, message, parent_id, fichier) VALUES (?, ?, ?, ?, NULL)",
                            (sujet_id, st.session_state.user["id"], reply_msg.strip(), comment["id"]),
                        )
                        conn.commit()
                        st.session_state[reply_open_key] = False
                        st.rerun()
                    else:
                        st.error("La réponse ne peut pas être vide.")

    for child in by_parent.get(comment["id"], []):
        render_comment(conn, child, by_parent, sujet_id, depth + 1)


conn = get_connection()

form_col_left, form_col_mid, form_col_right = st.columns([1, 2, 1])

with form_col_mid:
    st.title("Recherche")
    st.caption("Recherchez un ancien sujet ou une correction déjà publié. Aucun champ n'est obligatoire : renseignez-en un seul ou les trois pour affiner.")

    with st.form("recherche_form"):
        q = st.text_input("Matière", placeholder="ex : mathématiques, informatique...")
        type_doc = st.selectbox("Type", ["Tous", "sujet", "corrige"], format_func=lambda x: {"Tous": "Tous", "sujet": "Sujet", "corrige": "Corrigé"}[x])
        annee = st.text_input("Année", placeholder="2024")
        st.form_submit_button("Rechercher", use_container_width=True)

query = "SELECT s.*, u.firstname, u.lastname FROM sujets s JOIN users u ON u.id = s.user_id WHERE s.statut = 'valide'"
params: list = []

if type_doc != "Tous":
    query += " AND s.type = ?"
    params.append(type_doc)
if annee.strip():
    query += " AND s.annee = ?"
    params.append(annee.strip())

query += " ORDER BY s.created_at DESC"

sujets = conn.execute(query, params).fetchall()

if q.strip():
    sujets = [s for s in sujets if matches_search(q, s["matiere"])]

st.divider()

if not sujets:
    st.info("Aucun document trouvé.")

col_left, col_mid, col_right = st.columns([1, 4, 1])

with col_mid:
    for s in sujets:
        with st.container(border=True):
            badge = "Corrigé" if s["type"] == "corrige" else "Sujet"
            st.subheader(f"{s['matiere']}")
            st.write(f"{badge} · **{s['filiere']}** · {s['annee']}")
            st.caption(f"Déposé par {s['firstname']} {s['lastname']}")

            remote = storage.is_remote(s["fichier"])
            filepath = None if remote else UPLOAD_DIR / s["fichier"]
            ext = storage.extension_of(s["fichier"]) if remote else (s["fichier"].rsplit(".", 1)[-1].lower() if "." in s["fichier"] else "")
            file_available = remote or (filepath is not None and filepath.exists())

            if file_available:
                if ext in IMAGE_EXTENSIONS:
                    st.image(s["fichier"] if remote else str(filepath), width=300)
                else:
                    if remote:
                        st.markdown(
                            f'<iframe src="{s["fichier"]}" width="100%" height="500"></iframe>',
                            unsafe_allow_html=True,
                        )
                    else:
                        with open(filepath, "rb") as f:
                            b64 = base64.b64encode(f.read()).decode()
                        st.markdown(
                            f'<iframe src="data:application/pdf;base64,{b64}" width="100%" height="500"></iframe>',
                            unsafe_allow_html=True,
                        )

                comments_open_key = f"comments_open_{s['id']}"

                dl_col, comment_col, react_col = st.columns([1, 1, 1])
                with dl_col:
                    file_bytes = storage.fetch_bytes(s["fichier"]) if remote else filepath.read_bytes()
                    download_name = s["fichier"].split("?", 1)[0].rsplit("/", 1)[-1] if remote else s["fichier"]
                    st.download_button(
                        "Télécharger le fichier",
                        file_bytes,
                        file_name=download_name,
                        key=f"dl_{s['id']}",
                    )
                with comment_col:
                    n_comments = conn.execute(
                        "SELECT COUNT(*) AS cnt FROM commentaires WHERE sujet_id = ?", (s["id"],)
                    ).fetchone()["cnt"]
                    label = f"Commentaires ({n_comments})" if n_comments else "Commentaires"
                    if st.button(label, key=f"comments_toggle_{s['id']}"):
                        st.session_state[comments_open_key] = not st.session_state.get(comments_open_key, False)
                with react_col:
                    render_reactions(conn, "sujet", s["id"], key_prefix=f"sujet_{s['id']}")
            else:
                st.warning("Fichier introuvable sur le serveur.")
                comments_open_key = f"comments_open_{s['id']}"

            if st.session_state.get(comments_open_key, False):
                st.divider()
                all_comments = conn.execute(
                    "SELECT c.*, u.firstname, u.lastname FROM commentaires c "
                    "JOIN users u ON u.id = c.user_id "
                    "WHERE c.sujet_id = ? ORDER BY c.created_at ASC",
                    (s["id"],),
                ).fetchall()

                if not all_comments:
                    st.caption("Aucun commentaire pour l'instant.")
                else:
                    by_parent: dict = {}
                    for c in all_comments:
                        by_parent.setdefault(c["parent_id"], []).append(c)
                    for top_comment in by_parent.get(None, []):
                        render_comment(conn, top_comment, by_parent, s["id"])

                st.divider()

                if st.session_state.user:
                    st.markdown("**Ajouter un commentaire**")

                    comment_open_key = f"comment_open_{s['id']}"
                    comment_file_gen_key = f"comment_file_gen_{s['id']}"
                    if comment_file_gen_key not in st.session_state:
                        st.session_state[comment_file_gen_key] = 0

                    with st.container(key=f"comment_actions_row_{s['id']}"):
                        comment_btn_col, comment_upload_col = st.columns([1, 1], gap="small")
                        with comment_btn_col:
                            if st.button("Commenter", key=f"comment_btn_{s['id']}"):
                                st.session_state[comment_open_key] = not st.session_state.get(comment_open_key, False)
                        with comment_upload_col:
                            with st.container(key=f"comment_upload_compact_{s['id']}"):
                                quick_file = st.file_uploader(
                                    "Joindre un fichier",
                                    type=["pdf", "jpg", "jpeg", "png"],
                                    key=f"comment_quick_file_{s['id']}_{st.session_state[comment_file_gen_key]}",
                                    label_visibility="collapsed",
                                )
                        if quick_file is not None:
                            with st.spinner("Envoi du fichier..."):
                                file_url = storage.upload_file(quick_file.getvalue(), quick_file.name)
                            conn.execute(
                                "INSERT INTO commentaires (sujet_id, user_id, message, parent_id, fichier) VALUES (?, ?, ?, NULL, ?)",
                                (s["id"], st.session_state.user["id"], "", file_url),
                            )
                            conn.commit()
                            st.session_state[comment_file_gen_key] += 1
                            st.rerun()

                    if st.session_state.get(comment_open_key, False):
                        with st.form(key=f"comment_form_{s['id']}", clear_on_submit=True):
                            msg = st.text_area("Ajouter un commentaire", key=f"msg_{s['id']}", label_visibility="collapsed", placeholder="Votre commentaire...")
                            if st.form_submit_button("Envoyer"):
                                if msg.strip():
                                    conn.execute(
                                        "INSERT INTO commentaires (sujet_id, user_id, message, parent_id, fichier) VALUES (?, ?, ?, NULL, NULL)",
                                        (s["id"], st.session_state.user["id"], msg.strip()),
                                    )
                                    conn.commit()
                                    st.session_state[comment_open_key] = False
                                    st.rerun()
                                else:
                                    st.error("Le commentaire ne peut pas être vide.")
                else:
                    st.caption("Connectez-vous pour laisser un commentaire.")
