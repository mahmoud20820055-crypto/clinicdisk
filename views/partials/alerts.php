<?php if(isset($_SESSION['flash'])): ?>
<div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?= $_SESSION['flash']['type'] == 'success' ? 'check-circle' : ($_SESSION['flash']['type'] == 'danger' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>