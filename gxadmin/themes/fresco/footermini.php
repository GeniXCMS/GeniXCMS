<?= Site::loadLibFooter(); ?>
<?php echo Hooks::run('admin_footer_action', $data ?? []); ?>
</body>
</html>