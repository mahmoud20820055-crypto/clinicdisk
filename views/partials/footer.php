</div>
<!-- /.content-wrapper -->

<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>&copy; <?= date('Y') ?> <?= APP_NAME ?>.</strong> All rights reserved.
</footer>

</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/public/assets/adminlte/js/adminlte.min.js"></script>

<?php if(isset($_SESSION['flash'])): ?>
<script>
    $(document).ready(function() {
        let type = '<?= $_SESSION['flash']['type'] ?>';
        let message = '<?= $_SESSION['flash']['message'] ?>';
        if(type === 'success') {
            $('<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">' +
                message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>')
                .appendTo('body').delay(3000).fadeOut();
        } else if(type === 'danger') {
            $('<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">' +
                message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>')
                .appendTo('body').delay(5000).fadeOut();
        }
    });
</script>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

</body>
</html>