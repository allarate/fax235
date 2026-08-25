"""Accès base de données Turso (libSQL) pour Fax235."""

import threading
from pathlib import Path

import libsql
import streamlit as st

import auth

_DB_LOCK = threading.Lock()

BASE_DIR = Path(__file__).resolve().parent
UPLOAD_DIR = BASE_DIR / "uploads"

ADMIN_MATRICULE = "ADMIN0001"
ADMIN_PASSWORD_DEFAULT = "Admin@123"


class Row:
    """Émule sqlite3.Row : accès par index ou par nom de colonne."""

    def __init__(self, columns, values):
        self._columns = columns
        self._values = values

    def __getitem__(self, key):
        if isinstance(key, str):
            return self._values[self._columns.index(key)]
        return self._values[key]

    def get(self, key, default=None):
        try:
            return self[key]
        except (KeyError, ValueError, IndexError):
            return default

    def keys(self):
        return list(self._columns)

    def __repr__(self):
        return repr(dict(zip(self._columns, self._values)))


class Cursor:
    def __init__(self, native_cursor):
        self._columns = [d[0] for d in native_cursor.description] if native_cursor.description else []
        self._native = native_cursor

    def fetchone(self):
        row = self._native.fetchone()
        return Row(self._columns, list(row)) if row is not None else None

    def fetchall(self):
        return [Row(self._columns, list(r)) for r in self._native.fetchall()]


class Connection:
    """Émule l'API sqlite3.Connection utilisée par l'app, sur une base Turso."""

    def __init__(self, native_conn):
        self._conn = native_conn

    def execute(self, sql, params=None):
        with _DB_LOCK:
            return Cursor(self._conn.execute(sql, list(params) if params else []))

    def executescript(self, script):
        with _DB_LOCK:
            self._conn.executescript(script)

    def commit(self):
        with _DB_LOCK:
            self._conn.commit()


_thread_local = threading.local()


def get_connection():
    """Une connexion Turso par thread : Streamlit exécute chaque session sur son
    propre thread, et le client natif libsql n'est pas garanti thread-safe pour un
    partage global (une connexion unique en cache_resource provoquait des crashs
    aléatoires sous accès concurrents)."""
    conn = getattr(_thread_local, "conn", None)
    if conn is None:
        url = st.secrets["TURSO_DATABASE_URL"].replace("libsql://", "https://")
        token = st.secrets["TURSO_AUTH_TOKEN"]
        native_conn = libsql.connect(url, auth_token=token)
        conn = Connection(native_conn)
        conn.execute("PRAGMA foreign_keys = ON")
        _thread_local.conn = conn
    return conn


@st.cache_resource
def init_db():
    UPLOAD_DIR.mkdir(parents=True, exist_ok=True)
    conn = get_connection()
    conn.executescript(
        """
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            firstname TEXT NOT NULL,
            lastname TEXT NOT NULL,
            universite TEXT NOT NULL,
            matricule TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'etudiant' CHECK(role IN ('etudiant', 'admin')),
            photo TEXT,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS sujets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filiere TEXT NOT NULL,
            matiere TEXT NOT NULL,
            type TEXT NOT NULL CHECK(type IN ('sujet', 'corrige')),
            annee INTEGER NOT NULL,
            fichier TEXT NOT NULL,
            statut TEXT NOT NULL DEFAULT 'attente' CHECK(statut IN ('attente', 'valide', 'refuse')),
            user_id INTEGER NOT NULL REFERENCES users(id),
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS commentaires (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sujet_id INTEGER NOT NULL REFERENCES sujets(id) ON DELETE CASCADE,
            user_id INTEGER NOT NULL REFERENCES users(id),
            message TEXT NOT NULL,
            parent_id INTEGER REFERENCES commentaires(id) ON DELETE CASCADE,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS reactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            target_type TEXT NOT NULL CHECK(target_type IN ('sujet', 'commentaire')),
            target_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL REFERENCES users(id),
            emoji TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            UNIQUE(target_type, target_id, user_id, emoji)
        );

        CREATE TABLE IF NOT EXISTS sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            token TEXT NOT NULL UNIQUE,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );
        """
    )
    conn.commit()

    try:
        columns = [row["name"] for row in conn.execute("PRAGMA table_info(users)").fetchall()]
        if "photo" not in columns:
            conn.execute("ALTER TABLE users ADD COLUMN photo TEXT")
            conn.commit()

        comment_columns = [row["name"] for row in conn.execute("PRAGMA table_info(commentaires)").fetchall()]
        if "parent_id" not in comment_columns:
            conn.execute("ALTER TABLE commentaires ADD COLUMN parent_id INTEGER REFERENCES commentaires(id) ON DELETE CASCADE")
            conn.commit()
        if "fichier" not in comment_columns:
            conn.execute("ALTER TABLE commentaires ADD COLUMN fichier TEXT")
            conn.commit()
    except Exception:
        # Migrations déjà appliquées lors d'un déploiement précédent : une erreur ici
        # (ex. PRAGMA indisponible ponctuellement côté Turso) ne doit pas bloquer le démarrage.
        pass

    admin_exists = conn.execute(
        "SELECT 1 FROM users WHERE role = 'admin' LIMIT 1"
    ).fetchone()
    if not admin_exists:
        conn.execute(
            "INSERT INTO users (firstname, lastname, universite, matricule, password_hash, role) "
            "VALUES (?, ?, ?, ?, ?, 'admin')",
            (
                "Fax",
                "Administrateur",
                "Fax235",
                ADMIN_MATRICULE,
                auth.hash_password(ADMIN_PASSWORD_DEFAULT),
            ),
        )
        conn.commit()
