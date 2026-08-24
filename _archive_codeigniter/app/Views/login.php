<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Bifi</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="overlay">
    <h2>Connexion</h2>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (isset($error)) : ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="<?= base_url('auth/login') ?>" method="post">
        <input type="email" name="email" placeholder="Email" value="<?= set_value('email') ?>" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="<?= site_url('auth/register') ?>">Inscription</a></p>
</div>

</body>
</html>
