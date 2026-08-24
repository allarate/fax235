<header>
    <?= view('header') ?>
</header>
<div class="wrapper"> 
    <main id="consultation">
        <h2>Les differentes Universite du Tchad</h2> <br> <br>
        <div id='univ'>


          <form action="<?= site_url('auth/rechercher_univ_moundou') ?>" method="post">
                <button>Université de Moundou</button>
          </form>

            <form action="<?= site_url('auth/rechercher_univ_doba') ?>" method="post">
                <button>Université de Doba</button>
          </form>
            <form action="<?= site_url('auth/rechercher_univ_sarh') ?>" method="post">
                <button>Université de Sarh</button>
          </form>
            <form action="<?= site_url('auth/rechercher_univ_ndjamena') ?>" method="post">
                <button>Université de Ndjamena</button>
          </form>
          <form action="<?= site_url('auth/rechercher_univ_pala') ?>" method="post">
                <button>Université de Pala</button>
          </form>
          <form action="<?= site_url('auth/rechercher_univ_abeche') ?>" method="post">
                <button>Université d'Abeche</button>
          </form>
          <form action="<?= site_url('auth/rechercher_univ_ati') ?>" method="post">
                <button>Université d'Ati</button>
          </form>
          <form action="<?= site_url('auth/rechercher_univ_mongo') ?>" method="post">
                <button>Université de Mongo</button>
          </form>

          <form action="<?= site_url('auth/rechercher_univ_bol') ?>" method="post">
                <button>Université de Bol</button>
          </form>
          <form action="<?= site_url('auth/rechercher_univ_bongor') ?>" method="post">
                <button>Université de Bongor</button>
          </form>


        </div>


        <div id='concours'>
          <h2>Les differentes concours nationaux du Tchad</h2> <br> <br>
              <a href="<?= site_url('#') ?>">Ecole Normale Superieur de Bongor</a>
                <a href="<?= site_url('#') ?>">Ecole Normale Superieur de de Sarh/a>
              <a href="<?= site_url('#') ?>">Ecole Normale Superieur d'Abeche </a>
              <a href="<?= site_url('#') ?>">Ecole Normale Superieur de de Ndjamena</a>
                <a href="<?= site_url('#') ?>">Institut Universitaire de Science Technique d'Abeche</a>
              <a href="<?= site_url('#') ?>">Institut petro-chimie de Mao</a>
              <a href="<?= site_url('#') ?>">Medecine d'Abeche</a>
                <a href="<?= site_url('#') ?>">Medecine de Ndjamena</a>
        </div>
 </main>
</div>


<footer>
    <?= view('footer') ?>
</footer>


<style>
#univ {
  display: flex;
  flex-wrap: wrap; /* permet de passer à la ligne si trop long */
  gap: 10px; /* espace entre les boutons */
  align-items: center;
  justify-content: center; /* centre tout horizontalement */
  padding: 20px;
}

#univ form {
  margin: 0;
}

#univ button {
  padding: 8px 16px;
  background-color: #1e90ff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
}

#univ button:hover {
  background-color: #0d6efd;
}
</style>
