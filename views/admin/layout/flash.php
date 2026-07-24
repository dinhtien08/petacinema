<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible">
        <?= h($flash['message']) ?>
    </div>
<?php endif; ?>
