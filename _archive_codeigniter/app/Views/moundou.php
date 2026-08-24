<header>
    <?= view('header') ?>
</header>

<div class="wrapper"> 
    <main id="consultation">
    
        <!-- Carrousel d'images -->
        <div class="slider">
            <img src="<?= base_url('assets/images/udm1.png') ?>" class="active" alt="UDM 1">
            <img src="<?= base_url('assets/images/udm2.jpg') ?>" alt="UDM 2">
            <img src="<?= base_url('assets/images/udm3.jpeg') ?>" alt="UDM 3">
        </div>

        <h2>Liste des filières à l'Université de Moundou</h2>

        <?php if (!empty($filieres)): ?>
            <table>
                <thead>
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
            <p>Aucune filière trouvée pour l’Université de Moundou.</p>
        <?php endif; ?>
    </main>
</div>

<footer>
    <?= view('footer') ?>
</footer>
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.slider img');
    let index = 0;

    // S'assure que seule la première image a la classe active au départ
    images.forEach(img => img.classList.remove('active'));
    images[0].classList.add('active');

    setInterval(() => {
        images[index].classList.remove('active'); // masque l'image courante
        index = (index + 1) % images.length;     // passe à l'image suivante (boucle)
        images[index].classList.add('active');    // affiche la nouvelle image
    }, 5000); // 5000 ms = 5 secondes
});

</script>
     

