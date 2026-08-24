<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">
            <h2>Rechercher un Sujet ou Corrigé</h2>

            <form action="<?= site_url('auth/rechercher') ?>" method="get" style="margin-bottom: 20px;">
                <input type="search" id="recherche" name="q" placeholder="Rechercher (ex: bac D 2023 maths)" list="suggestions"
                    value="<?= esc($query ?? '') ?>" style="width: 60%; padding: 8px;">
                <datalist id="suggestions">
                    <option value="Mathematique">Mathématiques</option>
                    <option value="Physique chimie">Physique Chimie</option>
                    <option value="SVT">SVT</option>
                    <option value="Histoire geographie">Histoire Géographie</option>
                    <option value="Francais">Français</option>
                    <option value="Philosophie">Philosophie</option>
                    <option value="2023">2023</option>
                    <option value="Bac D">Bac D</option>
                    <option value="corrige">Corrigé</option>
                    <option value="sujet">Sujet</option>
                </datalist>
                <button type="submit" style="padding: 8px 16px;">Chercher</button>
            </form>

            <?php if (!empty($resultats)): ?>
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th>Matière</th>
                        <th>Série</th>
                        <th>Année</th>
                        <th>Type</th>
                        <th>Document</th>
                        <th>Discussion</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($resultats as $sujet): ?>
                        <tr id="sujet-<?= $sujet['id'] ?>">
                            <td><?= esc($sujet['matiere']) ?></td>
                            <td><?= esc($sujet['serie']) ?></td>
                            <td><?= esc($sujet['annee']) ?></td>
                            <td><?= ucfirst(esc($sujet['type'])) ?></td>
                            <td>
                                <?php if (!empty($sujet['fichier'])): ?>
                                    <a href="<?= base_url('uploads/' . $sujet['fichier']) ?>" target="_blank">📄 Voir</a><br>
                                    <a href="<?= base_url('uploads/' . $sujet['fichier']) ?>" download>📥 Télécharger</a>
                                <?php else: ?>
                                    <span style="color: red;">Aucun fichier</span>
                                <?php endif; ?>
                            </td>
                            <td style="min-width: 300px;">
                                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
                                    <?php if (!empty($sujet['messages'])): ?>
                                        <?php foreach ($sujet['messages'] as $msg): ?>
                                            <p><strong><?= esc($msg['auteur']) ?> :</strong> <?= esc($msg['message']) ?></p>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <em>Aucun message.</em>
                                    <?php endif; ?>
                                </div>



                                
                                <?php if (session()->get('logged_in')): ?>
                                    <form action="<?= site_url('auth/envoyer_message') ?>" method="post" style="margin-top: 10px;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="sujet_id" value="<?= esc($sujet['id']) ?>">
                                        <input type="hidden" name="query" value="<?= esc($query ?? '') ?>">
                                        <input type="text" name="message" placeholder="Écrivez un message..." required style="width: 100%; padding: 5px;">
                                        <button type="submit" style="margin-top: 5px;">Envoyer</button>
                                    </form>
                                <?php else: ?>
                                    <p><em>Connectez-vous pour discuter.</em></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (isset($query)): ?>
                <p>Aucun résultat trouvé pour : "<?= esc($query) ?>"</p>
            <?php endif; ?>
        </main>
    </div>
<footer>
    <?= view('footer') ?>
</footer>
