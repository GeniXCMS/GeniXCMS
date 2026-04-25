</div><!-- /#rb-main -->

<div id="rb-footer">
  <div>
    <strong>GeniXCMS</strong> &copy; 2014-<?php echo date('Y'); ?> &mdash;
    <?= _('All rights reserved.') ?>
  </div>
  <div style="display:flex;align-items:center;gap:12px">
    <span style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:4px;padding:2px 8px;font-size:.7rem;font-weight:600">
      v<?= System::$version ?>
    </span>
    <?php
    $time_taken = round(microtime(true) - $GLOBALS['start_time'], 4);
    echo '<span>' . _('Generated in') . ' ' . $time_taken . 's</span>';
    ?>
  </div>
</div>

<button id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-arrow-up"></i>
</button>

<script>
(function () {
  // ── Scroll-to-top button ──────────────────────────────────────────────────
  window.addEventListener('scroll', function () {
    document.getElementById('scrollTop').classList.toggle('visible', window.scrollY > 300);
  });

  // ── Close user dropdown on outside click ─────────────────────────────────
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.rb-user-wrap')) {
      document.querySelectorAll('.rb-user-wrap.open')
        .forEach(function (el) { el.classList.remove('open'); });
    }
  });

  // ── Tab switching (client-side, no reload) ────────────────────────────────
  // Clicking a tab swaps the active panel without a full page reload.
  // The URL is updated via history.pushState so the active tab persists on refresh.
  document.querySelectorAll('.rb-tab').forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      e.preventDefault();

      var targetTab = new URL(tab.href, location.href).searchParams.get('_rtab');
      if (!targetTab) return;

      // Update tab active state
      document.querySelectorAll('.rb-tab').forEach(function (t) {
        t.classList.toggle('active', t === tab);
      });

      // Update panel active state
      document.querySelectorAll('.rb-panel-content').forEach(function (p) {
        p.classList.toggle('active', p.dataset.tab === targetTab);
      });

      // Update URL without reload
      var params = new URLSearchParams(location.search);
      params.set('_rtab', targetTab);
      history.pushState(null, '', '?' + params.toString());
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