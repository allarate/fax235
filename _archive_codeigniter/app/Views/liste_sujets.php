<header>
    <?= view('header') ?>
</header>

<div class="wrapper"> 
    <main id="consultation">
        <h2>Validation des Sujets</h2>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f0f0f0;">
                <tr>
                    <th>Titre</th>
                    <th>Matière</th>
                    <th>Série</th>
                    <th>Année</th>
                    <th>Statut</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sujets as $sujet): ?>
                    <tr>
                        <td><?= esc($sujet['titre']) ?></td>
                        <td><?= esc($sujet['matiere']) ?></td>
                        <td><?= esc($sujet['serie']) ?></td>
                        <td><?= esc($sujet['annee']) ?></td>
                        <td style="color: <?= $sujet['statut'] === 'valide' ? 'green' : 'red' ?>;">
                            <?= esc($sujet['statut']) ?>
                        </td>
                        <td>
                            <a href="<?= base_url('uploads/' . $sujet['fichier']) ?>" target="_blank">📄 Voir</a>
                        </td>
                        <td>
                            <?php if ($sujet['statut'] !== 'valide'): ?>
                                <form method="post" action="<?= site_url('auth/valider_sujet/' . $sujet['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit">✅ Valider</button>
                                </form>
                            <?php else: ?>
                                <span style="color: grey;">✔️ Validé</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
 </main>
</div>
<footer>
    <?= view('footer') ?>
</footer>
