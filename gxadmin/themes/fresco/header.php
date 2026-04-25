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
   GeniXCMS Admin Theme: Fresco  v1.0.0
   Compact financial-dashboard style. Accent: lime-green #c8f04a
   ═══════════════════════════════════════════════════════════════ */
:root {
  --fr-sidebar-w:    210px;
  --fr-header-h:     48px;
  --fr-accent:       #c8f04a;
  --fr-accent-dark:  #a8d030;
  --fr-accent-text:  #1a2400;
  --fr-body-bg:      #f2f2f2;
  --fr-sidebar-bg:   #ffffff;
  --fr-header-bg:    #ffffff;
  --fr-border:       #e8e8e8;
  --fr-text:         #1a1a1a;
  --fr-muted:        #888888;
  --fr-section-lbl:  #aaaaaa;
  --fr-card-bg:      #ffffff;
  --fr-radius:       8px;
  --fr-radius-sm:    5px;
  --fr-font:         'Inter', system-ui, sans-serif;
  --fr-shadow:       0 1px 3px rgba(0,0,0,.06);
}
*, *::before, *::after { box-sizing: border-box }
html, body { height: 100%; margin: 0 }
body {
  font-family: var(--fr-font);
  background: var(--fr-body-bg);
  color: var(--fr-text);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  font-size: 13px;
}

/* ── LAYOUT ────────────────────────────────────────────────────── */
#fr-shell { display: flex; min-height: 100vh }

/* ── SIDEBAR ───────────────────────────────────────────────────── */
#fr-sidebar {
  width: var(--fr-sidebar-w);
  background: var(--fr-sidebar-bg);
  border-right: 1px solid var(--fr-border);
  position: fixed; top: 0; left: 0; bottom: 0;
  z-index: 200;
  display: flex; flex-direction: column;
  overflow-y: auto; overflow-x: hidden;
  scrollbar-width: thin;
  scrollbar-color: #e8e8e8 transparent;
  transition: transform .25s ease;
}
#fr-sidebar::-webkit-scrollbar { width: 3px }
#fr-sidebar::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 3px }

/* Logo area */
.fr-logo-area {
  padding: 14px 16px 10px;
  display: flex; align-items: center; gap: 8px;
  border-bottom: 1px solid var(--fr-border);
  flex-shrink: 0;
}
.fr-logo-icon {
  width: 26px; height: 26px;
  background: var(--fr-accent);
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.fr-logo-icon i { font-size: .85rem; color: var(--fr-accent-text) }
.fr-logo-name {
  font-size: .82rem; font-weight: 700; color: var(--fr-text);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Nav */
.fr-nav { padding: 8px 0; flex: 1 }
.fr-section-label {
  padding: 10px 16px 3px;
  font-size: .6rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .08em;
  color: var(--fr-section-lbl);
}
.fr-nav-item {
  display: flex; align-items: center; gap: 9px;
  padding: 6px 16px;
  font-size: .78rem; font-weight: 500;
  color: var(--fr-muted);
  text-decoration: none;
  border-left: 2px solid transparent;
  transition: all .12s;
  cursor: pointer;
  position: relative;
}
.fr-nav-item i { font-size: .82rem; width: 14px; text-align: center; flex-shrink: 0 }
.fr-nav-item span { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.fr-nav-item:hover { color: var(--fr-text); background: #f8f8f8 }
.fr-nav-item.active {
  color: var(--fr-accent-text);
  background: rgba(200,240,74,.15);
  border-left-color: var(--fr-accent);
  font-weight: 600;
}
.fr-nav-item.active i { color: var(--fr-accent-text) }
.fr-chevron { font-size: .6rem !important; width: auto !important; transition: transform .2s; color: var(--fr-section-lbl) !important }
.fr-nav-item.has-children.open .fr-chevron { transform: rotate(180deg) }

/* Children */
.fr-nav-children { display: none; background: #fafafa }
.fr-nav-children.open { display: block }
.fr-nav-children .fr-nav-item {
  padding: 5px 16px 5px 38px;
  font-size: .75rem;
}

/* Sidebar footer */
.fr-sidebar-footer {
  padding: 12px 16px;
  border-top: 1px solid var(--fr-border);
  flex-shrink: 0;
}
.fr-user-row {
  display: flex; align-items: center; gap: 8px;
  cursor: pointer; position: relative;
}
.fr-user-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  object-fit: cover; flex-shrink: 0;
  border: 1px solid var(--fr-border);
}
.fr-user-name { font-size: .75rem; font-weight: 600; color: var(--fr-text); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.fr-user-role { font-size: .65rem; color: var(--fr-muted) }
.fr-user-dropdown {
  display: none;
  position: absolute; bottom: calc(100% + 6px); left: 0; right: 0;
  background: #fff; border: 1px solid var(--fr-border);
  border-radius: var(--fr-radius-sm);
  box-shadow: 0 4px 16px rgba(0,0,0,.1);
  z-index: 300; overflow: hidden;
}
.fr-user-row.open .fr-user-dropdown { display: block }
.fr-user-dropdown a {
  display: flex; align-items: center; gap: 8px;
  padding: 7px 12px; font-size: .75rem;
  color: var(--fr-text); text-decoration: none;
  transition: background .1s;
}
.fr-user-dropdown a:hover { background: #f5f5f5 }
.fr-user-dropdown a.fr-danger { color: #e53e3e }
.fr-user-dropdown a.fr-danger:hover { background: #fff5f5 }
.fr-user-dropdown hr { margin: 3px 0; border: none; border-top: 1px solid var(--fr-border) }

/* ── TOP HEADER ────────────────────────────────────────────────── */
#fr-header {
  height: var(--fr-header-h);
  background: var(--fr-header-bg);
  border-bottom: 1px solid var(--fr-border);
  position: sticky; top: 0; z-index: 100;
  display: flex; align-items: center;
  padding: 0 20px; gap: 12px;
}
.fr-header-title {
  font-size: .8rem; font-weight: 600; color: var(--fr-text);
  flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.fr-search {
  display: flex; align-items: center; gap: 6px;
  background: #f5f5f5; border: 1px solid var(--fr-border);
  border-radius: var(--fr-radius-sm);
  padding: 5px 10px; width: 200px;
}
.fr-search i { font-size: .75rem; color: var(--fr-muted) }
.fr-search input {
  border: none; background: transparent; outline: none;
  font-size: .75rem; color: var(--fr-text); width: 100%;
  font-family: var(--fr-font);
}
.fr-search input::placeholder { color: var(--fr-muted) }
.fr-header-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px;
  background: var(--fr-accent); border: none;
  border-radius: var(--fr-radius-sm);
  font-size: .72rem; font-weight: 600;
  color: var(--fr-accent-text);
  cursor: pointer; text-decoration: none;
  transition: background .12s; white-space: nowrap;
}
.fr-header-btn:hover { background: var(--fr-accent-dark); color: var(--fr-accent-text) }
.fr-header-btn i { font-size: .75rem }
.fr-header-btn-ghost {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 10px;
  background: transparent; border: 1px solid var(--fr-border);
  border-radius: var(--fr-radius-sm);
  font-size: .72rem; font-weight: 500; color: var(--fr-muted);
  cursor: pointer; text-decoration: none;
  transition: all .12s; white-space: nowrap;
}
.fr-header-btn-ghost:hover { border-color: #ccc; color: var(--fr-text) }
.fr-mobile-toggle {
  display: none; background: none; border: none;
  cursor: pointer; padding: 4px; color: var(--fr-muted);
}

/* ── MAIN ──────────────────────────────────────────────────────── */
#fr-main {
  margin-left: var(--fr-sidebar-w);
  flex: 1; display: flex; flex-direction: column; min-height: 100vh;
}
#fr-content { padding: 20px 24px 32px; flex: 1 }

/* ── CARDS ─────────────────────────────────────────────────────── */
.fr-card {
  background: var(--fr-card-bg);
  border: 1px solid var(--fr-border);
  border-radius: var(--fr-radius);
  box-shadow: var(--fr-shadow);
}
.fr-card-header {
  padding: 12px 16px 10px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid var(--fr-border);
}
.fr-card-title { font-size: .78rem; font-weight: 600; color: var(--fr-text) }
.fr-card-body { padding: 14px 16px }

/* ── FOOTER ────────────────────────────────────────────────────── */
#fr-footer {
  padding: 10px 24px;
  border-top: 1px solid var(--fr-border);
  background: var(--fr-header-bg);
  display: flex; align-items: center; justify-content: space-between;
  font-size: .68rem; color: var(--fr-muted);
}

/* ── SCROLL TOP ────────────────────────────────────────────────── */
#scrollTop {
  position: fixed; bottom: 20px; right: 20px;
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--fr-accent); color: var(--fr-accent-text); border: none;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; opacity: 0; visibility: hidden;
  transition: all .3s; z-index: 999;
  box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
#scrollTop.visible { opacity: 1; visibility: visible }
#scrollTop:hover { background: var(--fr-accent-dark); transform: translateY(-2px) }

/* ── MOBILE ────────────────────────────────────────────────────── */
@media (max-width: 992px) {
  #fr-sidebar { transform: translateX(calc(-1 * var(--fr-sidebar-w))) }
  #fr-sidebar.open { transform: translateX(0) }
  #fr-main { margin-left: 0 }
  .fr-mobile-toggle { display: flex }
  #fr-content { padding: 14px 16px }
}
#fr-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,.25); z-index: 150;
}
#fr-overlay.show { display: block }

/* ── BOOTSTRAP OVERRIDES ───────────────────────────────────────── */
.card { border-radius: var(--fr-radius) !important; border-color: var(--fr-border) !important; box-shadow: var(--fr-shadow) !important }
.btn-primary { background: var(--fr-accent) !important; border-color: var(--fr-accent) !important; color: var(--fr-accent-text) !important; font-weight: 600 !important }
.btn-primary:hover { background: var(--fr-accent-dark) !important; border-color: var(--fr-accent-dark) !important }
.text-primary { color: #2d7a00 !important }
.bg-primary { background: var(--fr-accent) !important; color: var(--fr-accent-text) !important }
.bg-primary.bg-opacity-10 { background: rgba(200,240,74,.12) !important; color: #2d7a00 !important }
.badge.bg-primary { background: var(--fr-accent) !important; color: var(--fr-accent-text) !important }
a.text-primary { color: #2d7a00 !important }

/* ── CONTENT FADE ──────────────────────────────────────────────── */
#fr-content > * { animation: frFadeIn .18s ease-out }
@keyframes frFadeIn { from{opacity:0;transform:translateY(3px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body>
<?php
$_fr_page     = $_GET['page'] ?? '';
$_fr_mod      = $_GET['mod']  ?? '';
$_fr_username = (string) Session::val('username');
$_fr_avatar   = Site::$url . 'assets/images/user1-256x256.png';
$_fr_sitename = htmlspecialchars(Options::v('site_name') ?: 'GeniXCMS');

// URL matching helper — strict: all defined params must match,
// and undefined discriminating params must be absent from current URL
$frUrlMatches = function(array $cp) use ($_fr_page, $_fr_mod): bool {
    $defined = array_filter($cp, fn($v) => $v !== '');
    if (empty($defined)) return false;
    foreach ($defined as $k => $v) {
        $cur = match($k) {
            'page' => $_fr_page,
            'mod'  => $_fr_mod,
            'sel'  => $_GET['sel']  ?? '',
            'act'  => $_GET['act']  ?? '',
            'type' => $_GET['type'] ?? '',
            'view' => $_GET['view'] ?? '',
            default => $_GET[$k] ?? '',
        };
        if ($cur !== $v) return false;
    }
    if (!isset($defined['type'])) { $t = $_GET['type'] ?? ''; if ($t !== '' && $t !== 'post') return false; }
    if (!isset($defined['mod']))  { if ($_fr_mod !== '') return false; }
    if (!isset($defined['act']))  { $a = $_GET['act'] ?? ''; if ($a !== '') return false; }
    if (!isset($defined['sel']))  { $s = $_GET['sel'] ?? ''; if ($s !== '') return false; }
    return true;
};
?>

<div id="fr-overlay" onclick="frCloseSidebar()"></div>
<div id="fr-shell">

<!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
<aside id="fr-sidebar">

  <!-- Logo -->
  <div class="fr-logo-area">
    <div class="fr-logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
    <span class="fr-logo-name"><?= $_fr_sitename ?></span>
  </div>

  <!-- Navigation -->
  <nav class="fr-nav">
    <?php echo Hooks::run('admin_sidebar_start'); ?>

    <a href="index.php" class="fr-nav-item <?= !isset($_GET['page']) ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span><?= _('Dashboard') ?></span>
    </a>

    <?php
    $frPositions = ['main', 'management', 'settings', 'external'];
    $frLabels    = [
        'main'       => _('Content'),
        'management' => _('Management'),
        'settings'   => _('Settings'),
        'external'   => _('Extensions'),
    ];
    foreach ($frPositions as $frPos):
        $frItems = AdminMenu::getItems($frPos);
        if (empty($frItems)) continue;
        $frVisible = array_filter($frItems, fn($i) => User::access((string)($i['access'] ?? 6)));
        if (empty($frVisible)) continue;
    ?>
    <div class="fr-section-label"><?= $frLabels[$frPos] ?? $frPos ?></div>
    <?php foreach ($frVisible as $frItem):
        parse_str(parse_url($frItem['url'] ?? '', PHP_URL_QUERY) ?? '', $frItemQP);
        $frItemType = $frItemQP['type'] ?? '';
        $frCurType  = $_GET['type'] ?? '';
        $frOwnMatch = $frUrlMatches($frItemQP)
                   && ($frItemType !== '' || $frCurType === '' || $frCurType === 'post');

        $frChildMatch = false;
        $frVisChildren = array_filter($frItem['children'] ?? [], fn($c) => User::access((string)($c['access'] ?? 6)));
        if (!$frOwnMatch && !empty($frVisChildren)) {
            foreach ($frVisChildren as $frCh) {
                parse_str(parse_url($frCh['url'] ?? '', PHP_URL_QUERY) ?? '', $frChP);
                if ($frUrlMatches($frChP)) { $frChildMatch = true; break; }
            }
        }
        $frIsActive = $frOwnMatch || $frChildMatch;
        $frHasChildren = !empty($frVisChildren);
    ?>
    <?php if ($frHasChildren): ?>
      <div class="fr-nav-item has-children <?= $frIsActive ? 'open active' : '' ?>">
        <i class="<?= htmlspecialchars($frItem['icon']) ?>"></i>
        <span><?= htmlspecialchars($frItem['label']) ?></span>
        <i class="bi bi-chevron-down fr-chevron"></i>
      </div>
      <div class="fr-nav-children <?= $frIsActive ? 'open' : '' ?>">
        <?php foreach ($frVisChildren as $frCh):
            parse_str(parse_url($frCh['url'] ?? '', PHP_URL_QUERY) ?? '', $frChP);
            $frChActive = $frUrlMatches($frChP);
        ?>
        <a href="<?= htmlspecialchars($frCh['url']) ?>"
           class="fr-nav-item <?= $frChActive ? 'active' : '' ?>">
          <i class="<?= htmlspecialchars($frCh['icon'] ?? 'bi bi-dot') ?>"></i>
          <span><?= htmlspecialchars($frCh['label']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <a href="<?= htmlspecialchars($frItem['url']) ?>"
         class="fr-nav-item <?= $frIsActive ? 'active' : '' ?>">
        <i class="<?= htmlspecialchars($frItem['icon']) ?>"></i>
        <span><?= htmlspecialchars($frItem['label']) ?></span>
      </a>
    <?php endif; ?>
    <?php endforeach; endforeach; ?>

    <?php echo Hooks::run('admin_sidebar_end'); ?>
  </nav>

  <!-- User footer -->
  <div class="fr-sidebar-footer">
    <div class="fr-user-row" onclick="this.classList.toggle('open')">
      <img src="<?= $_fr_avatar ?>" class="fr-user-avatar" alt="">
      <div style="flex:1;min-width:0">
        <div class="fr-user-name"><?= htmlspecialchars($_fr_username) ?></div>
        <div class="fr-user-role"><?= _('Administrator') ?></div>
      </div>
      <i class="bi bi-three-dots-vertical" style="font-size:.7rem;color:var(--fr-muted)"></i>
      <div class="fr-user-dropdown">
        <a href="index.php?page=users&act=edit&id=<?= User::id(Session::val('username')) ?>&token=<?= TOKEN ?>">
          <i class="bi bi-person"></i> <?= _('My Profile') ?>
        </a>
        <a href="index.php?page=settings">
          <i class="bi bi-gear"></i> <?= _('Settings') ?>
        </a>
        <a href="<?= Site::$url ?>" target="_blank">
          <i class="bi bi-box-arrow-up-right"></i> <?= _('Visit Site') ?>
        </a>
        <hr>
        <a href="<?= Url::logout() ?>" class="fr-danger">
          <i class="bi bi-power"></i> <?= _('Logout') ?>
        </a>
      </div>
    </div>
  </div>

</aside>

<!-- ── MAIN ─────────────────────────────────────────────────────── -->
<div id="fr-main">

  <!-- Header -->
  <header id="fr-header">
    <button class="fr-mobile-toggle" onclick="frOpenSidebar()">
      <i class="bi bi-list" style="font-size:1.1rem"></i>
    </button>
    <span class="fr-header-title"><?= htmlspecialchars(Options::v('site_name') ?: 'Admin Dashboard') ?></span>
    <div class="fr-search">
      <i class="bi bi-search"></i>
      <input type="text" placeholder="<?= _('Search...') ?>" id="fr-search-input">
    </div>
    <?php echo Hooks::run('admin_header_top_right_action'); ?>
    <a href="<?= Site::$url ?>" target="_blank" class="fr-header-btn-ghost">
      <i class="bi bi-eye"></i> <?= _('Site') ?>
    </a>
    <a href="index.php?page=posts&act=add&type=post" class="fr-header-btn">
      <i class="bi bi-plus"></i> <?= _('New Post') ?>
    </a>
  </header>

  <!-- Content -->
  <div id="fr-content">