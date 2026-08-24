<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">
            <h2>Liste des filières à l'Université de Pala</h2>

            <?php if (!empty($filieres)): ?>
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #f0f0f0;">
                        <tr>
                            <th>Faculté</th>
                            <th>Filière</th>
                            <th>Université</th>
                            <th>Type de Bac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filieres as $filiere): ?>
                            <tr>
                                <td><?= esc($filiere['faculte'] ?? '-') ?></td>
                                <td><?= esc($filiere['nom']) ?></td>
                                <td><?= esc($filiere['universite']) ?></td>
                                <td><?= esc($filiere['bac']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune filière trouvée pour l’Université de Pala.</p>
            <?php endif; ?>
   </main>
</div>
<footer>
    <?= view('footer') ?>
</footer>
