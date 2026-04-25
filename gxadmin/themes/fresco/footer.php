  </div><!-- /#fr-content -->

  <div id="fr-footer">
    <span>GeniXCMS &copy; 2014-<?php echo date('Y'); ?></span>
    <div style="display:flex;align-items:center;gap:10px">
      <span style="background:#f0f0f0;border:1px solid #e0e0e0;border-radius:4px;padding:1px 7px;font-size:.65rem;font-weight:600">v<?= System::$version ?></span>
      <?php $t = round(microtime(true) - $GLOBALS['start_time'], 4); echo "<span>{$t}s</span>"; ?>
    </div>
  </div>

</div><!-- /#fr-main -->
</div><!-- /#fr-shell -->

<button id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-arrow-up" style="font-size:.75rem"></i>
</button>

<script>
(function () {
  // Scroll top
  window.addEventListener('scroll', function () {
    document.getElementById('scrollTop').classList.toggle('visible', window.scrollY > 300);
  });

  // Mobile sidebar
  window.frOpenSidebar = function () {
    document.getElementById('fr-sidebar').classList.add('open');
    document.getElementById('fr-overlay').classList.add('show');
  };
  window.frCloseSidebar = function () {
    document.getElementById('fr-sidebar').classList.remove('open');
    document.getElementById('fr-overlay').classList.remove('show');
  };

  // Close user dropdown on outside click
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.fr-user-row')) {
      document.querySelectorAll('.fr-user-row.open').forEach(function (el) { el.classList.remove('open'); });
    }
  });

  // Treeview toggle
  document.querySelectorAll('.fr-nav-item.has-children').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var isOpen = this.classList.toggle('open');
      var children = this.nextElementSibling;
      if (children && children.classList.contains('fr-nav-children')) {
        children.classList.toggle('open', isOpen);
      }
    });
  });

  // Search focus shortcut
  document.addEventListener('keydown', function (e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault();
      var inp = document.getElementById('fr-search-input');
      if (inp) inp.focus();
    }
  });
})();
</script>

<?php $versionUrl = Url::ajax('version'); ?>
<script>
setTimeout(function () {
  if (typeof $.getJSON === 'function') {
    $.getJSON('<?= $versionUrl ?>', function (obj) {
      if (obj.status == 'true') {
        window.showGxToast('<?= _("Update Available") ?> v' + obj.version, 'warning');
      }
    });
  }
}, 3000);
</script>

<?= Site::loadLibFooter(); ?>
<?php echo Hooks::run('admin_footer_action', $data ?? []); ?>
</body>
</html>