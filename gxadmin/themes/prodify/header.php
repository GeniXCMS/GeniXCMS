<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= Site::meta(['backend']); ?>
<?php if (!defined("OFFLINE_MODE") || !OFFLINE_MODE): ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<?php endif; ?>
<?= Site::loadLibHeader(); ?>
<?php echo Hooks::run('admin_header_action'); ?>
<?php if (Options::v('admin_custom_css')): ?>
<style><?= Options::v('admin_custom_css'); ?></style>
<?php endif; ?>
<style>
/* ═══════════════════════════════════════════════════════════════
   GeniXCMS Admin Theme: Prodify  v1.0.0
   Clean, modern SaaS-style layout inspired by productivity apps.
   ═══════════════════════════════════════════════════════════════ */
:root {
  --pd-sidebar-w:    240px;
  --pd-sidebar-bg:   #ffffff;
  --pd-sidebar-border: #f0f0f5;
  --pd-body-bg:      #f5f5fa;
  --pd-accent:       #7c3aed;
  --pd-accent-soft:  rgba(124,58,237,.08);
  --pd-accent-hover: #6d28d9;
  --pd-teal:         #0d9488;
  --pd-text:         #111827;
  --pd-muted:        #6b7280;
  --pd-border:       #e5e7eb;
  --pd-card-bg:      #ffffff;
  --pd-radius:       12px;
  --pd-radius-sm:    8px;
  --pd-font:         'Inter', system-ui, sans-serif;
  --pd-shadow:       0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --pd-shadow-md:    0 4px 12px rgba(0,0,0,.08);
}
*, *::before, *::after { box-sizing: border-box }
html, body { height: 100%; margin: 0 }
body {
  font-family: var(--pd-font);
  background: var(--pd-body-bg);
  color: var(--pd-text);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

/* ── LAYOUT SHELL ──────────────────────────────────────────────── */
#pd-shell {
  display: flex;
  min-height: 100vh;
}

/* ── SIDEBAR ───────────────────────────────────────────────────── */
#pd-sidebar {
  width: var(--pd-sidebar-w);
  background: var(--pd-sidebar-bg);
  border-right: 1px solid var(--pd-sidebar-border);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 200;
  transition: transform .25s cubic-bezier(.4,0,.2,1);
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: thin;
  scrollbar-color: #e5e7eb transparent;
}
#pd-sidebar::-webkit-scrollbar { width: 4px }
#pd-sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px }

/* Sidebar header — user profile */
.pd-sidebar-user {
  padding: 20px 16px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid var(--pd-sidebar-border);
  cursor: pointer;
  position: relative;
}
.pd-sidebar-user img {
  width: 36px; height: 36px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.pd-sidebar-user .pd-user-info { flex: 1; min-width: 0 }
.pd-sidebar-user .pd-user-name {
  font-size: .82rem; font-weight: 700;
  color: var(--pd-text); white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
}
.pd-sidebar-user .pd-user-status {
  font-size: .68rem; color: var(--pd-teal); font-weight: 500;
  display: flex; align-items: center; gap: 4px;
}
.pd-sidebar-user .pd-user-status::before {
  content: ''; width: 6px; height: 6px;
  background: var(--pd-teal); border-radius: 50%; flex-shrink: 0;
}
.pd-user-chevron { color: var(--pd-muted); font-size: .75rem; flex-shrink: 0 }

/* User dropdown */
.pd-user-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 4px); left: 12px; right: 12px;
  background: #fff;
  border: 1px solid var(--pd-border);
  border-radius: var(--pd-radius-sm);
  box-shadow: var(--pd-shadow-md);
  z-index: 300;
  overflow: hidden;
}
.pd-sidebar-user.open .pd-user-dropdown { display: block }
.pd-user-dropdown a {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  font-size: .78rem; color: var(--pd-text);
  text-decoration: none; transition: background .1s;
}
.pd-user-dropdown a:hover { background: var(--pd-body-bg) }
.pd-user-dropdown a.pd-danger { color: #ef4444 }
.pd-user-dropdown a.pd-danger:hover { background: #fef2f2 }
.pd-user-dropdown hr { margin: 4px 0; border: none; border-top: 1px solid var(--pd-border) }

/* Nav sections */
.pd-nav-section { padding: 16px 8px 4px }
.pd-nav-label {
  font-size: .65rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--pd-muted);
  padding: 0 8px; margin-bottom: 4px;
}
.pd-nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 7px 10px;
  border-radius: var(--pd-radius-sm);
  font-size: .82rem; font-weight: 500;
  color: var(--pd-muted);
  text-decoration: none;
  transition: all .15s;
  cursor: pointer;
  position: relative;
}
.pd-nav-item i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0 }
.pd-nav-item span { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.pd-nav-item:hover { background: var(--pd-body-bg); color: var(--pd-text) }
.pd-nav-item.active {
  background: var(--pd-accent-soft);
  color: var(--pd-accent);
  font-weight: 600;
}
.pd-nav-item.active i { color: var(--pd-accent) }
.pd-nav-badge {
  background: var(--pd-accent); color: #fff;
  font-size: .6rem; font-weight: 700;
  padding: 1px 6px; border-radius: 9999px;
  min-width: 18px; text-align: center;
}

/* Treeview children */
.pd-nav-children {
  display: none;
  padding-left: 28px;
}
.pd-nav-children.open { display: block }
.pd-nav-children .pd-nav-item {
  font-size: .78rem;
  padding: 5px 10px;
}
.pd-nav-item.has-children .bi-chevron-down {
  transition: transform .2s;
}
.pd-nav-item.has-children.open .bi-chevron-down {
  transform: rotate(180deg);
}

/* Sidebar footer — promo card */
.pd-sidebar-footer {
  margin: auto 12px 16px;
  background: linear-gradient(135deg, var(--pd-accent), #a855f7);
  border-radius: var(--pd-radius);
  padding: 16px;
  color: #fff;
}
.pd-sidebar-footer .pd-footer-title {
  font-size: .78rem; font-weight: 700; margin-bottom: 4px;
  display: flex; align-items: center; gap: 6px;
}
.pd-sidebar-footer .pd-footer-desc {
  font-size: .7rem; opacity: .85; line-height: 1.5; margin-bottom: 12px;
}
.pd-sidebar-footer .pd-footer-btn {
  display: inline-block;
  background: rgba(255,255,255,.2);
  border: 1px solid rgba(255,255,255,.3);
  color: #fff; font-size: .72rem; font-weight: 600;
  padding: 5px 14px; border-radius: 9999px;
  text-decoration: none; transition: background .15s;
}
.pd-sidebar-footer .pd-footer-btn:hover { background: rgba(255,255,255,.35) }

/* ── MAIN WRAPPER ──────────────────────────────────────────────── */
#pd-main {
  margin-left: var(--pd-sidebar-w);
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ── TOP BAR ───────────────────────────────────────────────────── */
#pd-topbar {
  background: transparent;
  padding: 14px 28px 0;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  position: sticky; top: 0; z-index: 100;
}
.pd-topbar-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px;
  background: #fff; border: 1px solid var(--pd-border);
  border-radius: 9999px;
  font-size: .78rem; font-weight: 500; color: var(--pd-text);
  text-decoration: none; cursor: pointer;
  transition: all .15s; white-space: nowrap;
  box-shadow: var(--pd-shadow);
}
.pd-topbar-btn:hover { border-color: var(--pd-accent); color: var(--pd-accent) }
.pd-topbar-btn i { font-size: .85rem }
.pd-topbar-btn.primary {
  background: var(--pd-accent); border-color: var(--pd-accent);
  color: #fff;
}
.pd-topbar-btn.primary:hover { background: var(--pd-accent-hover); border-color: var(--pd-accent-hover); color: #fff }

/* ── CONTENT BODY ──────────────────────────────────────────────── */
#pd-content {
  padding: 20px 28px 32px;
  flex: 1;
}

/* ── CARDS ─────────────────────────────────────────────────────── */
.pd-card {
  background: var(--pd-card-bg);
  border: 1px solid var(--pd-border);
  border-radius: var(--pd-radius);
  box-shadow: var(--pd-shadow);
  overflow: hidden;
}
.pd-card-header {
  padding: 16px 20px 12px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid var(--pd-border);
}
.pd-card-title {
  font-size: .88rem; font-weight: 700; color: var(--pd-text);
  display: flex; align-items: center; gap: 8px;
}
.pd-card-title i { color: var(--pd-muted); font-size: .9rem }
.pd-card-body { padding: 16px 20px }
.pd-card-footer {
  padding: 10px 20px;
  border-top: 1px solid var(--pd-border);
  background: #fafafa;
}

/* ── FOOTER ────────────────────────────────────────────────────── */
#pd-footer {
  padding: 12px 28px;
  border-top: 1px solid var(--pd-border);
  background: #fff;
  display: flex; align-items: center; justify-content: space-between;
  font-size: .72rem; color: var(--pd-muted);
}

/* ── SCROLL TOP ────────────────────────────────────────────────── */
#scrollTop {
  position: fixed; bottom: 24px; right: 24px;
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--pd-accent); color: #fff; border: none;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; opacity: 0; visibility: hidden;
  transition: all .3s; z-index: 999;
  box-shadow: 0 4px 12px rgba(124,58,237,.35);
}
#scrollTop.visible { opacity: 1; visibility: visible }
#scrollTop:hover { background: var(--pd-accent-hover); transform: translateY(-2px) }

/* ── MOBILE ────────────────────────────────────────────────────── */
@media (max-width: 992px) {
  #pd-sidebar {
    transform: translateX(calc(-1 * var(--pd-sidebar-w)));
  }
  #pd-sidebar.open { transform: translateX(0) }
  #pd-main { margin-left: 0 }
  #pd-content { padding: 16px }
  #pd-topbar { padding: 12px 16px 0 }
}

/* ── OVERLAY (mobile) ──────────────────────────────────────────── */
#pd-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.3);
  z-index: 150;
}
#pd-overlay.show { display: block }

/* ── CONTENT ANIMATIONS ────────────────────────────────────────── */
#pd-content > * { animation: pdFadeIn .2s ease-out }
@keyframes pdFadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }

/* ── BOOTSTRAP OVERRIDES ───────────────────────────────────────── */
.card { border-radius: var(--pd-radius) !important; border-color: var(--pd-border) !important }
.btn-primary { background: var(--pd-accent) !important; border-color: var(--pd-accent) !important }
.btn-primary:hover { background: var(--pd-accent-hover) !important; border-color: var(--pd-accent-hover) !important }
.badge.bg-primary { background: var(--pd-accent) !important }
.text-primary { color: var(--pd-accent) !important }
.bg-primary { background: var(--pd-accent) !important }
.bg-primary.bg-opacity-10 { background: var(--pd-accent-soft) !important }
a.text-primary { color: var(--pd-accent) !important }
</style>
</head>
<body>
<?php
$_pd_page    = $_GET['page'] ?? '';
$_pd_mod     = $_GET['mod']  ?? '';
$_pd_username = (string) Session::val('username');
$_pd_avatar   = Site::$url . 'assets/images/user1-256x256.png';
?>

<div id="pd-overlay" onclick="pdCloseSidebar()"></div>
<div id="pd-shell">

<!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
<aside id="pd-sidebar">

  <!-- User profile -->
  <div class="pd-sidebar-user" onclick="this.classList.toggle('open')">
    <img src="<?= $_pd_avatar ?>" alt="<?= htmlspecialchars($_pd_username) ?>">
    <div class="pd-user-info">
      <div class="pd-user-name"><?= htmlspecialchars($_pd_username) ?></div>
      <div class="pd-user-status"><?= _('Online') ?></div>
    </div>
    <i class="bi bi-chevron-down pd-user-chevron"></i>
    <div class="pd-user-dropdown">
      <a href="index.php?page=users&act=edit&id=<?= User::id(Session::val('username')) ?>&token=<?= TOKEN ?>">
        <i class="bi bi-person"></i> <?= _('My Profile') ?>
      </a>
      <a href="index.php?page=settings">
        <i class="bi bi-gear"></i> <?= _('Settings') ?>
      </a>
      <hr>
      <a href="<?= Url::logout() ?>" class="pd-danger">
        <i class="bi bi-power"></i> <?= _('Logout') ?>
      </a>
    </div>
  </div>

  <!-- Main Navigation -->
  <div class="pd-nav-section">
    <?php echo Hooks::run('admin_sidebar_start'); ?>
    <a href="index.php" class="pd-nav-item <?= !isset($_GET['page']) ? 'active' : '' ?>">
      <i class="bi bi-house-door"></i>
      <span><?= _('Home') ?></span>
    </a>
    <?php
    // Render all menu positions
    $pdPositions = ['main', 'management', 'settings', 'external'];
    foreach ($pdPositions as $pdPos):
        $pdItems = AdminMenu::getItems($pdPos);
        if (empty($pdItems)) continue;
        foreach ($pdItems as $pdItem):
            if (!User::access((string)($pdItem['access'] ?? 6))) continue;
            $pdIsActive = ($_pd_page === $pdItem['id'] || $_pd_mod === $pdItem['id']);
            // Also check if any child URL matches current request
            if (!$pdIsActive && !empty($pdItem['children'])) {
                foreach ($pdItem['children'] as $pdCheckChild) {
                    $pdCQ = parse_url($pdCheckChild['url'] ?? '', PHP_URL_QUERY) ?? '';
                    parse_str($pdCQ, $pdCP);
                    $pdDefinedParams = array_filter($pdCP, fn($v) => $v !== '');
                    if (empty($pdDefinedParams)) continue;
                    $pdAllMatch = true;
                    foreach ($pdDefinedParams as $k => $v) {
                        $pdCurr = match($k) {
                            'page' => $_pd_page,
                            'mod'  => $_pd_mod,
                            'sel'  => $_GET['sel']  ?? '',
                            'act'  => $_GET['act']  ?? '',
                            'type' => $_GET['type'] ?? '',
                            'view' => $_GET['view'] ?? '',
                            default => $_GET[$k] ?? '',
                        };
                        if ($pdCurr !== $v) { $pdAllMatch = false; break; }
                    }
                    if ($pdAllMatch) { $pdIsActive = true; break; }
                }
            }
            $pdHasChildren = !empty($pdItem['children']);
            $pdVisibleChildren = $pdHasChildren ? array_filter(
                $pdItem['children'],
                fn($c) => User::access((string)($c['access'] ?? 6))
            ) : [];
            $pdHasVisible = !empty($pdVisibleChildren);
    ?>
    <?php if ($pdHasVisible): ?>
      <div class="pd-nav-item has-children <?= $pdIsActive ? 'open active' : '' ?>">
        <i class="<?= htmlspecialchars($pdItem['icon']) ?>"></i>
        <span><?= htmlspecialchars($pdItem['label']) ?></span>
        <i class="bi bi-chevron-down pd-user-chevron" style="font-size:.65rem"></i>
      </div>
      <div class="pd-nav-children <?= $pdIsActive ? 'open' : '' ?>">
        <?php foreach ($pdVisibleChildren as $pdChild):
            // Parse child URL params for exact matching
            $pdChildQ = parse_url($pdChild['url'] ?? '', PHP_URL_QUERY) ?? '';
            parse_str($pdChildQ, $pdChildParams);
            $pdChildPage = $pdChildParams['page'] ?? '';
            $pdChildMod  = $pdChildParams['mod']  ?? '';
            $pdChildSel  = $pdChildParams['sel']  ?? '';
            $pdChildAct  = $pdChildParams['act']  ?? '';
            $pdChildType = $pdChildParams['type'] ?? '';

            // All defined params in child URL must match current URL exactly
            $pdChildActive = true;
            foreach (array_filter($pdChildParams, fn($v) => $v !== '') as $k => $v) {
                $pdCurrent = match($k) {
                    'page' => $_pd_page,
                    'mod'  => $_pd_mod,
                    'sel'  => $_GET['sel']  ?? '',
                    'act'  => $_GET['act']  ?? '',
                    'type' => $_GET['type'] ?? '',
                    'view' => $_GET['view'] ?? '',
                    default => $_GET[$k] ?? '',
                };
                if ($pdCurrent !== $v) { $pdChildActive = false; break; }
            }
            // Must have at least one meaningful param to match
            if (empty(array_filter($pdChildParams, fn($v) => $v !== ''))) {
                $pdChildActive = false;
            }
        ?>
        <a href="<?= htmlspecialchars($pdChild['url']) ?>"
           class="pd-nav-item <?= $pdChildActive ? 'active' : '' ?>">
          <i class="<?= htmlspecialchars($pdChild['icon'] ?? 'bi bi-dot') ?>"></i>
          <span><?= htmlspecialchars($pdChild['label']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <a href="<?= htmlspecialchars($pdItem['url']) ?>"
         class="pd-nav-item <?= $pdIsActive ? 'active' : '' ?>">
        <i class="<?= htmlspecialchars($pdItem['icon']) ?>"></i>
        <span><?= htmlspecialchars($pdItem['label']) ?></span>
      </a>
    <?php endif; ?>
    <?php endforeach; endforeach; ?>
    <?php echo Hooks::run('admin_sidebar_end'); ?>
  </div>

  <!-- Spacer -->
  <div style="flex:1"></div>

  <!-- Footer promo card -->
  <div class="pd-sidebar-footer">
    <div class="pd-footer-title">
      <i class="bi bi-stars"></i>
      <?= htmlspecialchars(Options::v('site_name') ?: 'GeniXCMS') ?>
    </div>
    <div class="pd-footer-desc">
      <?= _('Powered by GeniXCMS') ?> v<?= System::$version ?>
    </div>
    <a href="<?= Site::$url ?>" target="_blank" class="pd-footer-btn">
      <?= _('Visit Site') ?>
    </a>
  </div>

</aside><!-- /#pd-sidebar -->

<!-- ── MAIN ─────────────────────────────────────────────────────── -->
<div id="pd-main">

  <!-- Top bar -->
  <div id="pd-topbar">
    <button class="pd-topbar-btn d-lg-none" onclick="pdOpenSidebar()" style="border:none;background:#fff">
      <i class="bi bi-list"></i>
    </button>
    <div style="flex:1"></div>
    <?php echo Hooks::run('admin_header_top_right_action'); ?>
    <a href="<?= Site::$url ?>" target="_blank" class="pd-topbar-btn">
      <i class="bi bi-eye"></i> <?= _('Visit Site') ?>
    </a>
    <a href="index.php?page=settings" class="pd-topbar-btn">
      <i class="bi bi-gear"></i>
    </a>
  </div>

  <!-- Content -->
  <div id="pd-content">