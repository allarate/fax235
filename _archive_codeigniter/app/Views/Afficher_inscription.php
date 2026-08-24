<header>
    <?= view('header') ?>
</header>

<div class="overlay">
    <h2>Afficher Inscription étudiant</h2>

    <?php if (isset($message)): ?>
        <p style="color: red"><?= esc($message) ?></p>
    <?php endif; ?>

    <form action="<?= site_url('inscription/afficherInscriptionConsulation') ?>" method="post">
        <input type='text' name='nom' placeholder='nom'>
        <input type='text' name='email' placeholder='email'>
        <button>CHERCHER</button>
    </form>

    <?php if (isset($etudiant)): ?>
        <h3>Résultat :</h3>
        <p>Nom : <?= esc($etudiant['nom']) ?></p>
        <p>Email : <?= esc($etudiant['email']) ?></p>
    <?php endif; ?>
    <?php echo $etudiant['email'] ?>
    <?php echo $etudiant['id'] ?>
</div>

<footer>
    <?= view('footer') ?>
</footer>
