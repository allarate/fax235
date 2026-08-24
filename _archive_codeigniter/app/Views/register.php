<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Bifi</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="overlay">
    <h2>Inscription</h2>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($validation)) : ?>
        <div class="error">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('auth/register') ?>" method="post">
        <input type="text" name="lastname" placeholder="Nom" value="<?= set_value('lastname') ?>" required><br>
        <input type="text" name="firstname" placeholder="Prénom" value="<?= set_value('firstname') ?>" required><br>
        <input type="email" name="email" placeholder="Email" value="<?= set_value('email') ?>" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="<?= site_url('auth/login') ?>">Connexion</a></p>
</div>

</body>
</html>