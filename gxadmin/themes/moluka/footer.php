  </div><!-- /#mk-content -->

  <div id="mk-footer">
    <div>
      <strong>GeniXCMS</strong> &copy; 2014-<?php echo date('Y'); ?> &mdash; <?= _('All rights reserved.') ?>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <span style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:2px 8px;font-size:.68rem;font-weight:600">
        v<?= System::$version ?>
      </span>
      <?php
      $time_taken = round(microtime(true) - $GLOBALS['start_time'], 4);
      echo '<span>' . _('Generated in') . ' ' . $time_taken . 's</span>';
      ?>
    </div>
  </div>

</div><!-- /#mk-main -->
</div><!-- /#mk-shell -->

<button id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-arrow-up"></i>
</button>

<script>
(function () {
  // Scroll top
  window.addEventListener('scroll', function () {
    document.getElementById('scrollTop').classList.toggle('visible', window.scrollY > 300);
  });

  // Mobile sidebar
  window.mkOpenSidebar = function () {
    document.getElementById('mk-sidebar').classList.add('open');
    document.getElementById('mk-overlay').classList.add('show');
  };
  window.mkCloseSidebar = function () {
    document.getElementById('mk-sidebar').classList.remove('open');
    document.getElementById('mk-overlay').classList.remove('show');
  };

  // Close user dropdown on outside click
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.mk-sidebar-user')) {
      document.querySelectorAll('.mk-sidebar-user.open')
        .forEach(function (el) { el.classList.remove('open'); });
    }
  });

  // Treeview toggle — toggle .open on both the trigger and its next sibling (.mk-nav-children)
  document.querySelectorAll('.mk-nav-item.has-children').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var isOpen = this.classList.toggle('open');
      var children = this.nextElementSibling;
      if (children && children.classList.contains('mk-nav-children')) {
        children.classList.toggle('open', isOpen);
      }
    });
  });
})();
</script>

<?php
$versionUrl = Url::ajax('version');
?>
<script>
setTimeout(function () {
  if (typeof $.getJSON === 'function') {
    $.getJSON('<?= $versionUrl ?>', function (obj) {
      if (obj.status == 'true') {
        window.showGxToast(
          '<?= _("CMS Update Available") ?> — v' + obj.version + ' <?= _("is ready.") ?>',
          'warning'
        );
      }
    });
  }
}, 3000);
</script>

<?= Site::loadLibFooter(); ?>
<?php echo Hooks::run('admin_footer_action', $data ?? []); ?>
</body>
</html>