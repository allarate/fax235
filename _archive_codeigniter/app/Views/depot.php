<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">


   <h2>Proposer un nouveau sujet ou corrigé</h2>

            <?php if (session()->get('logged_in')): ?>

                <!-- Affichage des messages d'erreur ou succès -->
                <?php if(session()->getFlashdata('errors')): ?>
                    <ul style="color: red;">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if(session()->getFlashdata('error')): ?>
                    <p style="color: red;"><?= esc(session()->getFlashdata('error')) ?></p>
                <?php endif; ?>

                <?php if(session()->getFlashdata('success')): ?>
                    <p style="color: green;"><?= esc(session()->getFlashdata('success')) ?></p>
                <?php endif; ?>

                <form action="<?= site_url('auth/depot') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <input type="text" name="titre" placeholder="Titre du document" required value="<?= set_value('titre') ?>">
                    <br>
                    <select name="serie" required>
                        <option value="">Choisir la série</option>
                        <option value="A" <?= set_select('serie', 'A') ?>>Bac A</option>
                        <option value="C" <?= set_select('serie', 'C') ?>>Bac C</option>
                        <option value="D" <?= set_select('serie', 'D') ?>>Bac D</option>
                    </select>
                    <br>
                    <select name="matiere" required>
                        <option value="">Choisir la matière</option>
                        <option value="Mathematique" <?= set_select('matiere', 'Mathematique') ?>>Mathématiques</option>
                        <option value="Physique chimie" <?= set_select('matiere', 'Physique chimie') ?>>Physique Chimie</option>
                        <option value="SVT" <?= set_select('matiere', 'SVT') ?>>SVT</option>
                        <option value="Histoire geographie" <?= set_select('matiere', 'Histoire geographie') ?>>Histoire Géographie</option>
                        <option value="Francais" <?= set_select('matiere', 'Francais') ?>>Français</option>
                        <option value="Philosophie" <?= set_select('matiere', 'Philosophie') ?>>Philosophie</option>
                    </select>
                    <br>
                    <select name="type" required>
                        <option value="sujet" <?= set_select('type', 'sujet') ?>>Sujet</option>
                        <option value="corrige" <?= set_select('type', 'corrige') ?>>Corrigé</option>
                    </select>
                    <br>
                    <input type="number" name="annee" placeholder="Année (ex: 2023)" required min="2000" max="2099" value="<?= set_value('annee', date('Y')) ?>">
                    <br>
                    <input type="file" name="fichier" accept=".pdf,.jpg,.png" required>
                    <br>
                    <button type="submit">Déposer</button>

                </form>

                <p style="color: grey;">Votre document sera publié après validation par un administrateur.</p>

            <?php else: ?>
                <p>Connectez-vous pour proposer un document.</p>
            <?php endif; ?>
 </main>
</div>
<footer>
    <?= view('footer') ?>
</footer>
