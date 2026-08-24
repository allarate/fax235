<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Bifi</title>
</head>
<body>

<h1>Bienvenue <?= session()->get('firstname') ?> <?= session()->get('lastname') ?></h1>

<p>Email : <?= session()->get('email') ?></p>

<p><a href="<?= base_url('auth/logout') ?>">Se déconnecter</a></p>

</body>
</html>
