<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">
        <div id='filiere'>
            <h2>Ajouter une filière</h2>
            <form action="<?= site_url('auth/EnregistrerFiliere') ?>"  method="post" style="margin-bottom: 20px;">
        <!-- Filière -->
        <label for="faculte">Nom de la faculté</label>
        <input list="facultes" name="faculte" id="faculte" placeholder="Tapez ou sélectionnez une faculté" required>

        <datalist id="facultes">
            <option value="Faculté des Sciences Exactes et Appliquées">
            <option value="Faculté des Sciences Juridiques et Politiques">
            <option value="Faculté de Droit et Sciences Sociales">
            <option value="Faculté des Sciences Economiques et de Gestion">
            <option value="Faculté des Lettres, Arts et Sciences Humaines">
            <option value="Faculté des Sciences et Techniques d'Entreprise">
            <option value="Faculté des Lettres et Sciences Humaines">
            <option value="Faculté des Langues">
            <option value="Faculté des Sciences Humaines et Sociales">
            <option value="Faculté des Sciences de l’Education">
            <option value="Faculté des Langues, Lettres, Arts et Communication">
        </datalist>


        <br><br>
        <!-- Filière -->
        <label for="nom">Nom de la filière :</label>
        <input list="filieres" name="nom" id="nom" placeholder="Tapez ou sélectionnez une filière" required>

        <datalist id="filieres">
            <option value="Mathématiques">
            <option value="Informatique">
            <option value="Informatique-Télécommunication">
            <option value="Informatique Appliquée à la Gestion">
            <option value="Physique">
            <option value="Sciences Techniques">
            <option value="Chimie">
            <option value="Chimie Fondamentale">
            <option value="Chimie Appliquée">
            <option value="Biologie">
                <option value="Chimie-Biologie-Géologie">
            <option value="Génie agricole et Sécurité alimentaire">
            <option value="Mines et Génie Géologie">
            <option value="Sciences de la Vie et de la Terre(SVT)">
            <option value="Sciences de la Terre et de l’Univers(Geologie)">
            <option value="Histoire">
            <option value="Français">
            <option value="Philosophie">
            <option value="Technique Commerciale">
            <option value="Economie Monetaire Bancaire">
            <option value="Gestion des Ressources Humaines">
            <option value="Gestion Administative des Petites et Moyenne Organisations">
            <option value="Lettres Modernes">
            <option value="Géographie">
            <option value="Administration ct Gestion des Entreprises/AGE">
            <option value="Anthropologie">
            <option value="Sociologie-Anthropologie">
            <option value="Sociologie">
            <option value="Droit en Français">
            <option value="Droit en Arabe">
            <option value="Finance des entreprises">
            <option value="Sciences Économiques Option Économie Appliquée">
            <option value="Sciences Économiques Option Economie et Commerce International">
            <option value="Administration et Planification de l'Éducation">
            <option value="Fondements et Pratiques de l'Éducation option Enseignement">
            <option value="Fondements et Pratiques de l'Éducation option Orientation Scolaire">
            <option value="Curricula et Didactique option Curricula">
            <option value="Didactique option Didactique">
            <option value="Psychoéducation">
            <option value="Rééducation Scolaire">
        </datalist>

        <br><br>

        <!-- Université -->
        <label for="universite">Université</label>
        <input list="universites" name="universite" id="universite" placeholder="Tapez ou sélectionnez une université" required>

        <datalist id="universites">
            <option value="Université de Moundou">
            <option value="Université de Ndjamena">
            <option value="Université de Doba">
            <option value="Université de Sarh">
            <option value="Université de Pala">
            <option value="Université d'Abeché">
                <option value="Université d'Ati">
            <option value="Université de Bongor">
            <option value="Université de Mongo">
            <option value="Université de Bol">
            <option value="Université de Faya">
        </datalist>

        <br><br>

        <!-- Bac -->
        <label for="bac">Série du Bac :</label>
        <input list="bacs" name="bac" id="bac" placeholder="Tapez ou sélectionnez un bac" required>

        <datalist id="bacs">
            <option value="Bac A">
            <option value="Bac C">
            <option value="Bac D">
            <option value="Bac E">
            <option value="Bac F">
            <option value="Bac G1">
            <option value="Bac G2">
            <option value="Bac G3">
            <option value="Bac C, D">
            <option value="Bac C, D, E">
            <option value="Bac C, D, E, F">
            <option value="Bac C, D, E, G2">
            <option value="Bac C, D, E, G2, G3">
            <option value="Serie Confondue">
        </datalist>


                <button type="submit" style="padding: 8px 16px;">Ajouter</button>
            </form>
        </div>
 </main>
</div>
<footer>
    <?= view('footer') ?>
</footer>
