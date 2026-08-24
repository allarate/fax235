<header>
    <?= view('header') ?>
</header>

<h2>Espace de Discussion</h2>
<div id="chat-box">
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <p><strong><?= esc($msg['auteur']) ?>:</strong> <?= esc($msg['message']) ?></p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun message pour le moment.</p>
    <?php endif; ?>
</div>

<?php if (session()->get('logged_in')): ?>
    <form action="<?= site_url('auth/envoyer_message') ?>" method="post">
        <input type="text" name="message" placeholder="Écrivez ici..." required>
        <button type="submit">Envoyer</button>
    </form>
<?php else: ?>
    <p>Connectez-vous pour participer à la discussion.</p>
<?php endif; ?>

<footer>
    <?= view('footer') ?>
</footer>
