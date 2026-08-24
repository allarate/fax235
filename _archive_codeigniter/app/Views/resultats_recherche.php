<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">
        <h2>Résultats pour : <?= esc($query) ?></h2>

        <?php if (!empty($resultats)): ?>
            <ul>
                <?php foreach ($resultats as $sujet): ?>
                    <li>
                        <strong><?= esc($sujet['titre']) ?></strong><br>
                        Matière : <?= esc($sujet['matiere']) ?><br>
                        <a href="<?= base_url('uploads/' . $sujet['fichier']) ?>" target="_blank">Voir le fichier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun résultat trouvé.</p>
        <?php endif; ?>
</main>
</div>
<footer>
    <?= view('footer') ?>
</footer>
