

<!DOCTYPE html>
<html lang="en">

<body>
<meta charset="UTF-8">
<title>Bifi!</title>
<script src="<?= base_url('assets/js/emoji-button.min.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('assets/css/consultation.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/styleMoundou.css') ?>">
<!-- Bootstrap local -->
<link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">



<div class="menu">
    <ul>
        <li class="logo">
                    <img src="<?= base_url('assets/images/bifi2.png') ?>" alt="Logo Bifi">
        </li>


        <li class="menu-item"><a href="<?= site_url('auth/index') ?>">Accueil</a></li>
        <li class="menu-item"><a href="<?= site_url('auth/bac') ?>">Bacs & Corrigés</a></li>
        <li class="menu-item"><a href="<?= site_url('auth/orientation') ?>">Orientations Universitaire</a></li>
        <li class="menu-item">
                <?php if (session()->get('role') === 'admin'): ?>
                    <a href="<?= site_url('auth/liste_sujets_admin') ?>">👑 Valider des Sujets</a>
                    <a href="<?= site_url('auth/Ajouter_Filiere') ?>">👑 Entrer une filiere</a>
                <?php endif; ?>
        </li>



        <li>
            <?php if (session()->get('logged_in')) : ?>
                <form id="photoForm" action="<?= site_url('auth/upload_photo') ?>" method="post" enctype="multipart/form-data" style="display:none;">
                    <input type="file" name="photo" id="photoInput" accept="image/*" onchange="document.getElementById('photoForm').submit()">
                </form>

                <?php
                $photo = session()->get('photo') ? base_url('uploads/' . session()->get('photo')) : base_url('assets/images/stigman.png');
                $photo_url = $photo . '?t=' . time();
                ?>

                <img src="<?= $photo_url ?>" alt="Photo de profil"
                     style="height: 50px; width: 50px; border-radius: 50%; object-fit: cover; margin-right: 10px; cursor: pointer;"
                     onclick="document.getElementById('photoInput').click()">

                <strong><?= esc(session()->get('firstname')) ?></strong>&nbsp;&nbsp;
                <button><a href="<?= site_url('auth/logout') ?>">Déconnexion</a></button>
            <?php else : ?>
                <a href="<?= site_url('auth/login') ?>">Connexion</a>
            <?php endif; ?>
        </li>
    </ul>
</div>

