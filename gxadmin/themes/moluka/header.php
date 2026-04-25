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
   GeniXCMS Admin Theme: moluka  v1.0.0
   Clean, modern SaaS-style layout inspired by productivity apps.
   ═══════════════════════════════════════════════════════════════ */
:root {
  --mk-sidebar-w:    240px;
  --mk-sidebar-bg:   #ffffff;
  --mk-sidebar-border: #f0f0f5;
  --mk-body-bg:      #f5f5fa;
  --mk-accent:       #00A3EA;
  --mk-accent-soft:  rgba(0,163,234,.08);
  --mk-accent-hover: #0090d0;
  --mk-teal:         #0d9488;
  --mk-text:         #111827;
  --mk-muted:        #6b7280;
  --mk-border:       #e5e7eb;
  --mk-card-bg:      #ffffff;
  --mk-radius:       12px;
  --mk-radius-sm:    8px;
  --mk-font:         'Inter', system-ui, sans-serif;
  --mk-shadow:       0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --mk-shadow-md:    0 4px 12px rgba(0,0,0,.08);
}
*, *::before, *::after { box-sizing: border-box }
html, body { height: 100%; margin: 0 }
body {
  font-family: var(--mk-font);
  background: var(--mk-body-bg);
  color: var(--mk-text);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

/* ── LAYOUT SHELL ──────────────────────────────────────────────── */
#mk-shell {
  display: flex;
  min-height: 100vh;
}

/* ── SIDEBAR ───────────────────────────────────────────────────── */
#mk-sidebar {
  width: var(--mk-sidebar-w);
  background: var(--mk-sidebar-bg);
  border-right: 1px solid var(--mk-sidebar-border);
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
#mk-sidebar::-webkit-scrollbar { width: 4px }
#mk-sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px }

/* Sidebar header — user profile */
.mk-sidebar-user {
  padding: 20px 16px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid var(--mk-sidebar-border);
  cursor: pointer;
  position: relative;
}
.mk-sidebar-user img {
  width: 36px; height: 36px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.mk-sidebar-user .mk-user-info { flex: 1; min-width: 0 }
.mk-sidebar-user .mk-user-name {
  font-size: .82rem; font-weight: 700;
  color: var(--mk-text); white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
}
.mk-sidebar-user .mk-user-status {
  font-size: .68rem; color: var(--mk-teal); font-weight: 500;
  display: flex; align-items: center; gap: 4px;
}
.mk-sidebar-user .mk-user-status::before {
  content: ''; width: 6px; height: 6px;
  background: var(--mk-teal); border-radius: 50%; flex-shrink: 0;
}
.mk-user-chevron { color: var(--mk-muted); font-size: .75rem; flex-shrink: 0 }

/* User dropdown */
.mk-user-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 4px); left: 12px; right: 12px;
  background: #fff;
  border: 1px solid var(--mk-border);
  border-radius: var(--mk-radius-sm);
  box-shadow: var(--mk-shadow-md);
  z-index: 300;
  overflow: hidden;
}
.mk-sidebar-user.open .mk-user-dropdown { display: block }
.mk-user-dropdown a {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  font-size: .78rem; color: var(--mk-text);
  text-decoration: none; transition: background .1s;
}
.mk-user-dropdown a:hover { background: var(--mk-body-bg) }
.mk-user-dropdown a.mk-danger { color: #ef4444 }
.mk-user-dropdown a.mk-danger:hover { background: #fef2f2 }
.mk-user-dropdown hr { margin: 4px 0; border: none; border-top: 1px solid var(--mk-border) }

/* Nav sections */
.mk-nav-section { padding: 16px 8px 4px }
.mk-nav-label {
  font-size: .65rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--mk-muted);
  padding: 0 8px; margin-bottom: 4px;
}
.mk-nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 7px 10px;
  border-radius: var(--mk-radius-sm);
  font-size: .82rem; font-weight: 500;
  color: var(--mk-muted);
  text-decoration: none;
  transition: all .15s;
  cursor: pointer;
  position: relative;
}
.mk-nav-item i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0 }
.mk-nav-item span { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.mk-nav-item:hover { background: var(--mk-body-bg); color: var(--mk-text) }
.mk-nav-item.active {
  background: var(--mk-accent-soft);
  color: var(--mk-accent);
  font-weight: 600;
}
.mk-nav-item.active i { color: var(--mk-accent) }
.mk-nav-badge {
  background: var(--mk-accent); color: #fff;
  font-size: .6rem; font-weight: 700;
  padding: 1px 6px; border-radius: 9999px;
  min-width: 18px; text-align: center;
}

/* Treeview children */
.mk-nav-children {
  display: none;
  padding-left: 28px;
}
.mk-nav-children.open { display: block }
.mk-nav-children .mk-nav-item {
  font-size: .78rem;
  padding: 5px 10px;
}
.mk-nav-item.has-children .bi-chevron-down {
  transition: transform .2s;
}
.mk-nav-item.has-children.open .bi-chevron-down {
  transform: rotate(180deg);
}

/* Sidebar footer — promo card */
.mk-sidebar-footer {
  margin: auto 12px 16px;
  background: linear-gradient(135deg, var(--mk-accent), #38bdf8);
  border-radius: var(--mk-radius);
  padding: 16px;
  color: #fff;
}
.mk-sidebar-footer .mk-footer-title {
  font-size: .78rem; font-weight: 700; margin-bottom: 4px;
  display: flex; align-items: center; gap: 6px;
}
.mk-sidebar-footer .mk-footer-desc {
  font-size: .7rem; opacity: .85; line-height: 1.5; margin-bottom: 12px;
}
.mk-sidebar-footer .mk-footer-btn {
  display: inline-block;
  background: rgba(255,255,255,.2);
  border: 1px solid rgba(255,255,255,.3);
  color: #fff; font-size: .72rem; font-weight: 600;
  padding: 5px 14px; border-radius: 9999px;
  text-decoration: none; transition: background .15s;
}
.mk-sidebar-footer .mk-footer-btn:hover { background: rgba(255,255,255,.35) }

/* ── MAIN WRAPPER ──────────────────────────────────────────────── */
#mk-main {
  margin-left: var(--mk-sidebar-w);
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ── TOP BAR ───────────────────────────────────────────────────── */
#mk-topbar {
  background: #fff;
  border-bottom: 1px solid var(--mk-border);
  padding: 0 28px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.mk-topbar-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px;
  background: #fff; border: 1px solid var(--mk-border);
  border-radius: 9999px;
  font-size: .78rem; font-weight: 500; color: var(--mk-text);
  text-decoration: none; cursor: pointer;
  transition: all .15s; white-space: nowrap;
  box-shadow: var(--mk-shadow);
}
.mk-topbar-btn:hover { border-color: var(--mk-accent); color: var(--mk-accent) }
.mk-topbar-btn i { font-size: .85rem }
.mk-topbar-btn.primary {
  background: var(--mk-accent); border-color: var(--mk-accent);
  color: #fff;
}
.mk-topbar-btn.primary:hover { background: var(--mk-accent-hover); border-color: var(--mk-accent-hover); color: #fff }

/* ── CONTENT BODY ──────────────────────────────────────────────── */
#mk-content {
  padding: 20px 28px 32px;
  flex: 1;
}

/* ── CARDS ─────────────────────────────────────────────────────── */
.mk-card {
  background: var(--mk-card-bg);
  border: 1px solid var(--mk-border);
  border-radius: var(--mk-radius);
  box-shadow: var(--mk-shadow);
  overflow: hidden;
}
.mk-card-header {
  padding: 16px 20px 12px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid var(--mk-border);
}
.mk-card-title {
  font-size: .88rem; font-weight: 700; color: var(--mk-text);
  display: flex; align-items: center; gap: 8px;
}
.mk-card-title i { color: var(--mk-muted); font-size: .9rem }
.mk-card-body { padding: 16px 20px }
.mk-card-footer {
  padding: 10px 20px;
  border-top: 1px solid var(--mk-border);
  background: #fafafa;
}

/* ── FOOTER ────────────────────────────────────────────────────── */
#mk-footer {
  padding: 12px 28px;
  border-top: 1px solid var(--mk-border);
  background: #fff;
  display: flex; align-items: center; justify-content: space-between;
  font-size: .72rem; color: var(--mk-muted);
}

/* ── SCROLL TOP ────────────────────────────────────────────────── */
#scrollTop {
  position: fixed; bottom: 24px; right: 24px;
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--mk-accent); color: #fff; border: none;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; opacity: 0; visibility: hidden;
  transition: all .3s; z-index: 999;
  box-shadow: 0 4px 12px rgba(0,163,234,.35);
}
#scrollTop.visible { opacity: 1; visibility: visible }
#scrollTop:hover { background: var(--mk-accent-hover); transform: translateY(-2px) }

/* ── MOBILE ────────────────────────────────────────────────────── */
@media (max-width: 992px) {
  #mk-sidebar {
    transform: translateX(calc(-1 * var(--mk-sidebar-w)));
  }
  #mk-sidebar.open { transform: translateX(0) }
  #mk-main { margin-left: 0 }
  #mk-content { padding: 16px }
  #mk-topbar { padding: 0 16px; }
}

/* ── OVERLAY (mobile) ──────────────────────────────────────────── */
#mk-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.3);
  z-index: 150;
}
#mk-overlay.show { display: block }

/* ── CONTENT ANIMATIONS ────────────────────────────────────────── */
#mk-content > * { animation: pdFadeIn .2s ease-out }
@keyframes pdFadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }

/* ── QUICK ACTION BUTTONS (dashboard) ─────────────────────────── */
.mk-quick-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 16px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 9999px;
  font-size: .8rem; font-weight: 600;
  color: #374151;
  text-decoration: none;
  box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  transition: border-color .15s, color .15s, box-shadow .15s;
  white-space: nowrap;
}
.mk-quick-btn:hover {
  border-color: #00A3EA;
  color: #00A3EA;
  box-shadow: 0 2px 8px rgba(0,163,234,.15);
  text-decoration: none;
}
.mk-quick-btn i { font-size: .82rem }
.mk-quick-btn-primary {
  background: #00A3EA;
  border-color: #00A3EA;
  color: #fff;
}
.mk-quick-btn-primary:hover {
  background: #0090d0;
  border-color: #0090d0;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,163,234,.3);
}

/* ── BOOTSTRAP OVERRIDES ───────────────────────────────────────── */
.card { border-radius: var(--mk-radius) !important; border-color: var(--mk-border) !important }
.btn-primary { background: var(--mk-accent) !important; border-color: var(--mk-accent) !important }
.btn-primary:hover { background: var(--mk-accent-hover) !important; border-color: var(--mk-accent-hover) !important }
.badge.bg-primary { background: var(--mk-accent) !important }
.text-primary { color: var(--mk-accent) !important }
.bg-primary { background: var(--mk-accent) !important }
.bg-primary.bg-opacity-10 { background: var(--mk-accent-soft) !important }
a.text-primary { color: var(--mk-accent) !important }
</style>
</head>
<body>
<?php
$_pd_page    = $_GET['page'] ?? '';
$_pd_mod     = $_GET['mod']  ?? '';
$_pd_username = (string) Session::val('username');
$_pd_avatar   = Site::$url . 'assets/images/user1-256x256.png';
?>

<div id="mk-overlay" onclick="mkCloseSidebar()"></div>
<div id="mk-shell">

<!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
<aside id="mk-sidebar">

  <!-- User profile -->
  <div class="mk-sidebar-user" onclick="this.classList.toggle('open')">
    <img src="<?= $_pd_avatar ?>" alt="<?= htmlspecialchars($_pd_username) ?>">
    <div class="mk-user-info">
      <div class="mk-user-name"><?= htmlspecialchars($_pd_username) ?></div>
      <div class="mk-user-status"><?= _('Online') ?></div>
    </div>
    <i class="bi bi-chevron-down mk-user-chevron"></i>
    <div class="mk-user-dropdown">
      <a href="index.php?page=users&act=edit&id=<?= User::id(Session::val('username')) ?>&token=<?= TOKEN ?>">
        <i class="bi bi-person"></i> <?= _('My Profile') ?>
      </a>
      <a href="index.php?page=settings">
        <i class="bi bi-gear"></i> <?= _('Settings') ?>
      </a>
      <hr>
      <a href="<?= Url::logout() ?>" class="mk-danger">
        <i class="bi bi-power"></i> <?= _('Logout') ?>
      </a>
    </div>
  </div>

  <!-- Main Navigation -->
  <div class="mk-nav-section">
    <?php echo Hooks::run('admin_sidebar_start'); ?>
    <a href="index.php" class="mk-nav-item <?= !isset($_GET['page']) ? 'active' : '' ?>">
      <i class="bi bi-house-door"></i>
      <span><?= _('Home') ?></span>
    </a>
    <?php
    // Helper: check if all defined params in a parsed URL match the current request
    $pdUrlMatches = function(array $cp) use ($_pd_page, $_pd_mod): bool {
        $defined = array_filter($cp, fn($v) => $v !== '');
        if (empty($defined)) return false;
        foreach ($defined as $k => $v) {
            $cur = match($k) {
                'page' => $_pd_page,
                'mod'  => $_pd_mod,
                'sel'  => $_GET['sel']  ?? '',
                'act'  => $_GET['act']  ?? '',
                'type' => $_GET['type'] ?? '',
                'view' => $_GET['view'] ?? '',
                default => $_GET[$k] ?? '',
            };
            if ($cur !== $v) return false;
        }
        // If child URL has no 'type', current URL must also have no type (or default 'post')
        // This prevents ?page=posts matching when current URL is ?page=posts&type=nixomers
        if (!isset($defined['type'])) {
            $curType = $_GET['type'] ?? '';
            if ($curType !== '' && $curType !== 'post') return false;
        }
        // If child URL has no 'mod', current URL must also have no mod
        if (!isset($defined['mod'])) {
            $curMod = $_pd_mod;
            if ($curMod !== '') return false;
        }
        // If child URL has no 'act', current URL must also have no act
        if (!isset($defined['act'])) {
            $curAct = $_GET['act'] ?? '';
            if ($curAct !== '') return false;
        }
        // If child URL has no 'sel', current URL must also have no sel
        if (!isset($defined['sel'])) {
            $curSel = $_GET['sel'] ?? '';
            if ($curSel !== '') return false;
        }
        return true;
    };

    // Render all menu positions
    $pdPositions = ['main', 'management', 'settings', 'external'];
    foreach ($pdPositions as $pdPos):
        $pdItems = AdminMenu::getItems($pdPos);
        if (empty($pdItems)) continue;
        foreach ($pdItems as $pdItem):
            if (!User::access((string)($pdItem['access'] ?? 6))) continue;

            // Parse parent's own URL
            parse_str(parse_url($pdItem['url'] ?? '', PHP_URL_QUERY) ?? '', $pdItemQP);

            // Parent is active only if its own URL matches AND current URL has
            // no extra params that would belong to a different item.
            // Key rule: if parent URL has no 'type', current 'type' must be empty or 'post'.
            $pdItemType    = $pdItemQP['type'] ?? '';
            $pdCurrentType = $_GET['type'] ?? '';
            $pdOwnMatch    = $pdUrlMatches($pdItemQP)
                          && ($pdItemType !== '' || $pdCurrentType === '' || $pdCurrentType === 'post');

            // Check children — but only count a child match if NO OTHER top-level
            // item also claims that child (i.e. the child's 'mod' or unique 'type' ties it here).
            $pdChildMatch = false;
            if (!$pdOwnMatch && !empty($pdItem['children'])) {
                foreach ($pdItem['children'] as $pdCheckChild) {
                    parse_str(parse_url($pdCheckChild['url'] ?? '', PHP_URL_QUERY) ?? '', $pdCP);
                    if ($pdUrlMatches($pdCP)) {
                        $pdChildMatch = true;
                        break;
                    }
                }
            }

            $pdIsActive = $pdOwnMatch || $pdChildMatch;
            $pdHasChildren = !empty($pdItem['children']);
            $pdVisibleChildren = $pdHasChildren ? array_filter(
                $pdItem['children'],
                fn($c) => User::access((string)($c['access'] ?? 6))
            ) : [];
            $pdHasVisible = !empty($pdVisibleChildren);
    ?>
    <?php if ($pdHasVisible): ?>
      <div class="mk-nav-item has-children <?= $pdIsActive ? 'open active' : '' ?>">
        <i class="<?= htmlspecialchars($pdItem['icon']) ?>"></i>
        <span><?= htmlspecialchars($pdItem['label']) ?></span>
        <i class="bi bi-chevron-down mk-user-chevron" style="font-size:.65rem"></i>
      </div>
      <div class="mk-nav-children <?= $pdIsActive ? 'open' : '' ?>">
        <?php foreach ($pdVisibleChildren as $pdChild):
            // Use the same strict matching function for child active state
            parse_str(parse_url($pdChild['url'] ?? '', PHP_URL_QUERY) ?? '', $pdChildParams);
            $pdChildActive = $pdUrlMatches($pdChildParams);
        ?>
        <a href="<?= htmlspecialchars($pdChild['url']) ?>"
           class="mk-nav-item <?= $pdChildActive ? 'active' : '' ?>">
          <i class="<?= htmlspecialchars($pdChild['icon'] ?? 'bi bi-dot') ?>"></i>
          <span><?= htmlspecialchars($pdChild['label']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <a href="<?= htmlspecialchars($pdItem['url']) ?>"
         class="mk-nav-item <?= $pdIsActive ? 'active' : '' ?>">
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
  <div class="mk-sidebar-footer">
    <div class="mk-footer-title">
      <i class="bi bi-stars"></i>
      <?= htmlspecialchars(Options::v('site_name') ?: 'GeniXCMS') ?>
    </div>
    <div class="mk-footer-desc">
      <?= _('Powered by GeniXCMS') ?> v<?= System::$version ?>
    </div>
    <a href="<?= Site::$url ?>" target="_blank" class="mk-footer-btn">
      <?= _('Visit Site') ?>
    </a>
  </div>

</aside><!-- /#mk-sidebar -->

<!-- ── MAIN ─────────────────────────────────────────────────────── -->
<div id="mk-main">

  <!-- Top bar -->
  <div id="mk-topbar">
    <button class="mk-topbar-btn d-lg-none" onclick="mkOpenSidebar()" style="border:none;background:#fff">
      <i class="bi bi-list"></i>
    </button>
    <div style="flex:1"></div>
    <?php echo Hooks::run('admin_header_top_right_action'); ?>
    <a href="<?= Site::$url ?>" target="_blank" class="mk-topbar-btn">
      <i class="bi bi-eye"></i> <?= _('Visit Site') ?>
    </a>
    <a href="index.php?page=settings" class="mk-topbar-btn">
      <i class="bi bi-gear"></i>
    </a>
  </div>

  <!-- Content -->
  <div id="mk-content">