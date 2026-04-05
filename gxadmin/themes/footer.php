    </main>
    <footer class="admin-footer d-flex justify-content-between align-items-center">
        <div>
            <strong>Copyright &copy; 2014-<?=date('Y');?> <a href="https://genixcms.web.id" target="_blank" class="text-decoration-none">GeniXCMS</a>.</strong>
            <?=_("All rights reserved.");?>
        </div>
        <div class="d-none d-sm-block">
            <span class="badge bg-light text-dark border">Version <?=System::$version;?></span>
            <?php
            $time_taken = round(microtime(true) - $GLOBALS['start_time'], 4);
            echo '<small class="ms-2 text-muted">'._("Generated in").' '.$time_taken.'s</small>';
            ?>
        </div>
    </footer>
</div>

<button id="scrollTop" class="btn btn-primary shadow-lg">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
$(function() {
    // Sidebar Toggles
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('#main-wrapper').toggleClass('expanded');
    });

    $('#sidebarClose').on('click', function() {
        $('#sidebar').removeClass('active');
    });

    // Mobile Sidebar Toggle (different behavior)
    if ($(window).width() < 992) {
        $('#sidebarToggle').on('click', function(e) {
            e.stopPropagation();
            $('#sidebar').toggleClass('active');
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#sidebar').length) {
                $('#sidebar').removeClass('active');
            }
        });
    }

    // Treeview Toggle
    $('.has-tree').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('open');
        $(this).find('.bi-chevron-down').toggleClass('rotate-180');
    });

    // Scroll Top
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) $('#scrollTop').addClass('visible');
        else $('#scrollTop').removeClass('visible');
    });
    $('#scrollTop').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 400);
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
    .rotate-180 { transform: rotate(180deg); }
    .has-tree .bi-chevron-down { transition: transform 0.3s; }
    #sidebar.active { margin-left: 0 !important; }
    @media (max-width: 992px) {
        #sidebar { margin-left: -260px; height: 100vh; }
    }
</style>

<?php
// Editor assets are now handled modularly via Editor class or module hooks

// Version Check (Modernized toastr call)
$versionUrl = Url::ajax('version');
?>
<script>
    setTimeout(function() {
        $.getJSON('<?=$versionUrl;?>', function(obj) {
            if (obj.status == 'true') {
                window.showGxToast('<?=_("CMS Update Available");?> — v' + obj.version + ' <?=_("is ready to download.");?>', 'warning');
            }
        });
    }, 3000);
</script>

    <?= Site::loadLibFooter(); ?>
    <?php echo Hooks::run('admin_footer_action', $data); ?>

</body>
</html>
