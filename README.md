# Fax235

Application **Streamlit** d'entraide entre étudiants tchadiens : dépôt et consultation
d'anciens sujets/corrigés d'examen, avec commentaires.

## Fonctionnalités

- **Publier** un ancien sujet ou une correction (fichier PDF/JPG/PNG) — visible
  immédiatement dans la recherche, sans validation préalable.
- **Rechercher** des sujets/corrigés (titre, matière, série, année).
- **Commenter** un sujet ou un corrigé (étudiants connectés).
- **Administration** : retirer un document publié (modération a posteriori), gérer les
  rôles des utilisateurs.

## Installation

```bash
pip install -r requirements.txt
```

## Lancement

```bash
streamlit run app.py
```

L'application crée automatiquement, au premier lancement :
- une base SQLite dans `data/faxuniversite.db` ;
- un dossier `uploads/` pour les fichiers déposés ;
- un compte administrateur par défaut : matricule `ADMIN0001` / mot de passe `Admin@123`
  (à changer après la première connexion, via la promotion d'un autre compte puis
  suppression de celui-ci, ou en modifiant directement la base).

## Structure du projet

```
app.py                 # Point d'entrée, navigation, thème
db.py                  # Connexion SQLite + schéma
auth.py                # Hachage / vérification des mots de passe
views/
  sujets.py             # Page d'accueil (cartes Publier / Recherche)
  login.py               # Connexion (matricule + mot de passe)
  register.py             # Inscription
  recherche.py             # Recherche des sujets/corrigés + commentaires
  publier.py                # Dépôt d'un nouveau sujet/corrigé (étudiant connecté)
  admin.py                   # Modération des documents publiés, gestion des utilisateurs
data/                   # Base SQLite (créée automatiquement)
uploads/                # Fichiers déposés par les étudiants
assets/                 # Images (logo)
.streamlit/config.toml  # Thème (couleurs, police, rayons)
_archive_codeigniter/   # Ancienne version du projet (CodeIgniter 4 / PHP), conservée pour référence
```

## Thème

Les couleurs (bordeaux), la police (Outfit pour les titres, Inter pour le texte) et les rayons de
coin sont définis dans [`.streamlit/config.toml`](.streamlit/config.toml). Le logo (sceau circulaire
Fax235, fond transparent) se trouve dans [`assets/fax235_logo.png`](assets/fax235_logo.png).

## Notes

- L'ancienne version PHP/CodeIgniter du projet est conservée dans
  [`_archive_codeigniter/`](_archive_codeigniter/) à titre de référence, mais n'est plus utilisée.
