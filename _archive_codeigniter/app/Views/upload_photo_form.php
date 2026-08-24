<h2>Changer la photo de profil</h2>

<?php if(session()->getFlashdata('error')): ?>
    <p style="color:red;"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>
<?php if(session()->getFlashdata('success')): ?>
    <p style="color:green;"><?= session()->getFlashdata('success') ?></p>
<?php endif; ?>

<form action="<?= site_url('auth/upload_photo') ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="photo" required accept="image/*">
    <button type="submit">Envoyer</button>
</form>
