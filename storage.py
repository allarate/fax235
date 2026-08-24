"""Stockage des fichiers (sujets/corrections, photos de profil) sur Cloudinary."""

import uuid

import cloudinary
import cloudinary.uploader
import requests
import streamlit as st

IMAGE_EXTENSIONS = {"jpg", "jpeg", "png"}


def _configure():
    cloudinary.config(
        cloud_name=st.secrets["CLOUDINARY_CLOUD_NAME"],
        api_key=st.secrets["CLOUDINARY_API_KEY"],
        api_secret=st.secrets["CLOUDINARY_API_SECRET"],
        secure=True,
    )


def upload_file(file_bytes: bytes, filename: str) -> str:
    """Envoie un fichier sur Cloudinary et retourne son URL publique."""
    _configure()
    ext = filename.rsplit(".", 1)[-1].lower() if "." in filename else ""
    resource_type = "image" if ext in IMAGE_EXTENSIONS else "raw"
    if resource_type == "image":
        # Cloudinary appends the detected image format to the public_id on its own.
        public_id = uuid.uuid4().hex
    else:
        public_id = f"{uuid.uuid4().hex}.{ext}" if ext else uuid.uuid4().hex
    result = cloudinary.uploader.upload(
        file_bytes,
        resource_type=resource_type,
        folder="fax235",
        public_id=public_id,
    )
    return result["secure_url"]


def fetch_bytes(url: str) -> bytes:
    """Télécharge le contenu d'un fichier stocké sur Cloudinary."""
    response = requests.get(url, timeout=15)
    response.raise_for_status()
    return response.content


def is_remote(file_ref: str) -> bool:
    return file_ref.startswith("http://") or file_ref.startswith("https://")


def extension_of(file_ref: str) -> str:
    name = file_ref.split("?", 1)[0].rsplit("/", 1)[-1]
    return name.rsplit(".", 1)[-1].lower() if "." in name else ""
