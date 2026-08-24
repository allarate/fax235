<header>
    <?= view('header') ?>
</header>

<div class="overlay">
    <h2>Inscription etudiant</h2>
    <form action="<?= site_url('inscription/inscriptionstudent') ?>" method="post">
        <input type="text" name="nom" placeholder="Nom" ><br>
        <input type="text" name="prenom" placeholder="Prénom" ><br>
        <input type="email" name="email" placeholder="Email" ><br>
        <input type="password" name="password" placeholder="Mot de passe"><br>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe"><br>
        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="<?= site_url('auth/login') ?>">Connexion</a></p>
</div>

<footer>
    <?= view('footer') ?>
</footer>