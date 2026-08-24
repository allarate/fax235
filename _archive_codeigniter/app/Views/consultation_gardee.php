<header>
    <?= view('header') ?>
</header>

<div class="container mt-5">

        <h2>Rechercher un Sujet ou Corrigé</h2>

        <form action="<?= site_url('auth/rechercher') ?>" method="get">
            <input type="search" id="recherche" name="q" placeholder="Rechercher" list="suggestions">
        <datalist id="suggestions">
        <option value="Mathematique">Mathématiques</option>
                <option value="Physique chimie">Physique Chimie</option>
                <option value="SVT">SVT</option>
                <option value="Histoire geographie">Histoire Géographie</option>
                <option value="Francais">Français</option>
                <option value="Philosophie">Philosophie</option>
        </datalist>
            <!-- Série -->
            <select name="serie">
                <option value="">Toutes les séries</option>
                <option value="A">Bac A</option>
                <option value="C">Bac C</option>
                <option value="D">Bac D</option>
            </select>

            <!-- Matière -->
            <select name="matiere">
                <option value="">Toutes les matières</option>
                <option value="Mathematique">Mathématiques</option>
                <option value="Physique chimie">Physique Chimie</option>
                <option value="SVT">SVT</option>
                <option value="Histoire geographie">Histoire Géographie</option>
                <option value="Francais">Français</option>
                <option value="Philosophie">Philosophie</option>
            </select>

            <!-- Type -->
            <select name="type">
                <option value="">Tous</option>
                <option value="sujet">Sujets</option>
                <option value="corrige">Corrigés</option>
            </select>

            <button type="submit" class="btn btn-success">Chercher</button>
        </form>
 </div>
<footer>
    <?= view('footer') ?>
</footer>
