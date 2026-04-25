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
/* ═══════════════════════════════════════════════════════
   GeniXCMS Ribbon Admin Theme v1.1.0
   ═══════════════════════════════════════════════════════ */
:root {
  --rb-accent:     #2563eb;
  --rb-accent-dk:  #1d4ed8;
  --rb-title-bg:   #1e293b;
  --rb-title-h:    36px;
  --rb-tab-h:      32px;
  --rb-panel-h:    90px;
  --rb-total-h:    calc(var(--rb-title-h) + var(--rb-tab-h) + var(--rb-panel-h));
  --rb-border:     #d1d5db;
  --rb-text:       #1e293b;
  --rb-muted:      #64748b;
  --rb-radius:     6px;
  --rb-font:       'Inter', system-ui, sans-serif;
}
*,*::before,*::after { box-sizing: border-box }
body { margin:0; font-family:var(--rb-font); background:#f1f5f9; color:var(--rb-text); overflow-x:hidden }

/* ── TITLE BAR ─────────────────────────────────────── */
#rb-titlebar {
  height: var(--rb-title-h);
  background: var(--rb-title-bg);
  display: flex; align-items: center;
  padding: 0 12px; gap: 10px;
  position: fixed; top:0; left:0; right:0; z-index:1100;
  user-select: none;
}
.rb-logo { display:flex; align-items:center; gap:7px; text-decoration:none; color:#fff; font-weight:700; font-size:.82rem; flex-shrink:0 }
.rb-logo img { height:20px; width:auto }
.rb-sitename { color:#94a3b8; font-size:.72rem; font-weight:500; flex:1; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.rb-title-actions { display:flex; align-items:center; gap:5px; flex-shrink:0 }
.rb-title-btn {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 9px; border-radius:4px;
  border:1px solid rgba(255,255,255,.14);
  background:rgba(255,255,255,.07); color:#cbd5e1;
  font-size:.7rem; font-weight:500; text-decoration:none; cursor:pointer;
  transition:all .15s; white-space:nowrap;
}
.rb-title-btn:hover { background:rgba(255,255,255,.17); color:#fff; border-color:rgba(255,255,255,.28) }
.rb-title-btn i { font-size:.78rem }
/* User dropdown */
.rb-user-wrap { position:relative }
.rb-user-menu {
  display:none; position:absolute; top:calc(100% + 6px); right:0;
  background:#fff; border:1px solid var(--rb-border); border-radius:var(--rb-radius);
  box-shadow:0 8px 24px rgba(0,0,0,.13); min-width:190px; z-index:2000; overflow:hidden;
}
.rb-user-wrap.open .rb-user-menu { display:block }
.rb-um-head { padding:9px 13px; border-bottom:1px solid #f1f5f9; background:#f8fafc }
.rb-um-name { font-weight:700; font-size:.8rem; color:var(--rb-text) }
.rb-um-role { font-size:.68rem; color:var(--rb-muted) }
.rb-user-menu a { display:flex; align-items:center; gap:7px; padding:7px 13px; font-size:.78rem; color:var(--rb-text); text-decoration:none; transition:background .1s }
.rb-user-menu a:hover { background:#f1f5f9 }
.rb-user-menu a.rb-danger { color:#ef4444 }
.rb-user-menu a.rb-danger:hover { background:#fef2f2 }
.rb-user-menu hr { margin:3px 0; border:none; border-top:1px solid #f1f5f9 }

/* ── TAB BAR ───────────────────────────────────────── */
#rb-tabbar {
  height: var(--rb-tab-h);
  background: #e2e8f0;
  border-bottom: 1px solid var(--rb-border);
  display: flex; align-items: flex-end;
  padding: 0 6px; gap: 1px;
  position: fixed; top:var(--rb-title-h); left:0; right:0; z-index:1090;
  overflow-x: auto; overflow-y: hidden;
  scrollbar-width: none;
}
#rb-tabbar::-webkit-scrollbar { display:none }
.rb-tab {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 13px 6px;
  font-size: .75rem; font-weight: 600;
  color: var(--rb-muted);
  background: transparent; border: 1px solid transparent;
  border-bottom: none; border-radius: 5px 5px 0 0;
  cursor: pointer; text-decoration: none;
  transition: all .14s; white-space: nowrap;
  position: relative; bottom: -1px; flex-shrink: 0;
}
.rb-tab:hover { background:rgba(255,255,255,.55); color:var(--rb-text) }
.rb-tab.active {
  background: #fff;
  border-color: var(--rb-border);
  border-bottom-color: #fff;
  color: var(--rb-accent);
}
.rb-tab.active::after {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:var(--rb-accent); border-radius:2px 2px 0 0;
}
.rb-tab i { font-size:.82rem }

/* ── RIBBON PANEL ──────────────────────────────────── */
#rb-panel {
  height: var(--rb-panel-h);
  background: #fff;
  border-bottom: 1px solid var(--rb-border);
  position: fixed; top:calc(var(--rb-title-h) + var(--rb-tab-h)); left:0; right:0; z-index:1080;
  overflow-x: auto; overflow-y: hidden;
  scrollbar-width: none;
}
#rb-panel::-webkit-scrollbar { display:none }
.rb-panel-content { display:none; align-items:stretch; height:100%; padding:0 6px; min-width:max-content }
.rb-panel-content.active { display:flex }

/* Ribbon Group — wraps a set of related buttons */
.rb-group {
  display: flex; flex-direction: column; align-items: stretch;
  padding: 5px 8px 0; min-width: 52px;
  border-right: 1px solid #e9ecef;
  position: relative;
}
.rb-group:last-child { border-right: none }
.rb-group-label {
  font-size: .58rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .07em; color: var(--rb-muted);
  position: absolute; bottom: 4px; left: 0; right: 0;
  text-align: center; white-space: nowrap; pointer-events: none;
}
.rb-group-btns {
  display: flex; align-items: flex-start; gap: 2px;
  flex: 1; padding-bottom: 18px; flex-wrap: nowrap;
}

/* Large ribbon button — icon on top, label below */
.rb-btn {
  display: inline-flex; flex-direction: column; align-items: center; justify-content: flex-start;
  gap: 3px; padding: 5px 5px 4px; width: 58px;
  border: 1px solid transparent; border-radius: 4px;
  background: transparent; color: var(--rb-text);
  font-size: .6rem; font-weight: 500; text-decoration: none; cursor: pointer;
  transition: all .12s; white-space: normal; text-align: center; line-height: 1.25;
  word-break: break-word; overflow-wrap: break-word; hyphens: auto;
  flex-shrink: 0;
}
.rb-btn i { font-size: 1.3rem; line-height: 1; color: var(--rb-muted); transition: color .12s; flex-shrink: 0 }
.rb-btn:hover { background: #f1f5f9; border-color: #d1d5db; color: var(--rb-text) }
.rb-btn:hover i { color: var(--rb-accent) }
.rb-btn.rb-active { background: #dbeafe; border-color: #bfdbfe; color: var(--rb-accent) }
.rb-btn.rb-active i { color: var(--rb-accent) }

/* Separator */
.rb-sep { width:1px; background:#e9ecef; margin:6px 3px; align-self:stretch; flex-shrink:0 }

/* ── MAIN CONTENT ──────────────────────────────────── */
#rb-main {
  margin-top: var(--rb-total-h);
  padding: 24px 28px;
  min-height: calc(100vh - var(--rb-total-h));
}

/* ── FOOTER ────────────────────────────────────────── */
#rb-footer {
  background: #fff; border-top: 1px solid var(--rb-border);
  padding: 7px 28px;
  display: flex; align-items: center; justify-content: space-between;
  font-size: .7rem; color: var(--rb-muted);
}

/* ── SCROLL TOP ────────────────────────────────────── */
#scrollTop {
  position:fixed; bottom:22px; right:22px;
  width:36px; height:36px; border-radius:50%;
  background:var(--rb-accent); color:#fff; border:none;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; opacity:0; visibility:hidden;
  transition:all .3s; z-index:999;
  box-shadow:0 4px 12px rgba(37,99,235,.3);
}
#scrollTop.visible { opacity:1; visibility:visible }
#scrollTop:hover { background:var(--rb-accent-dk); transform:translateY(-2px) }

/* ── RESPONSIVE ────────────────────────────────────── */
@media(max-width:768px) {
  :root { --rb-panel-h:0px }
  #rb-panel, #rb-tabbar { display:none }
  #rb-main { margin-top:var(--rb-title-h); padding:16px }
  .rb-sitename { display:none }
}

/* ── FADE IN ───────────────────────────────────────── */
#rb-main > * { animation:rbFadeIn .22s ease-out }
@keyframes rbFadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body>
<?php
$_rb_page = $_GET['page'] ?? '';
$_rb_mod  = $_GET['mod']  ?? '';
$_rb_tab  = $_GET['_rtab'] ?? '';

// ── BUILD FLAT TAB LIST FROM ALL MENU POSITIONS ──────────────────────────────
// Each top-level menu item becomes one tab.
// Tab ID = menu item ID.
$_rb_all_items = array_merge(
    AdminMenu::getItems('main'),
    AdminMenu::getItems('management'),
    AdminMenu::getItems('settings'),
    AdminMenu::getItems('external')
);

// Filter by access
$_rb_all_items = array_values(array_filter(
    $_rb_all_items,
    fn($i) => User::access((string)($i['access'] ?? 6))
));

// Auto-detect active tab from current URL if not set
if (empty($_rb_tab)) {
    foreach ($_rb_all_items as $item) {
        // Check item's own URL
        $q = parse_url($item['url'] ?? '', PHP_URL_QUERY) ?? '';
        parse_str($q, $cp);
        if (rbUrlMatches($cp, $_rb_page, $_rb_mod)) {
            $_rb_tab = $item['id'];
            break;
        }
        // Check children URLs
        foreach ($item['children'] ?? [] as $child) {
            $q = parse_url($child['url'] ?? '', PHP_URL_QUERY) ?? '';
            parse_str($q, $cp);
            if (rbUrlMatches($cp, $_rb_page, $_rb_mod)) {
                $_rb_tab = $item['id'];
                break 2;
            }
        }
    }
    if (empty($_rb_tab)) {
        $_rb_tab = 'home';
    }
}

// ── HELPERS ──────────────────────────────────────────────────────────────────
/**
 * Large ribbon button: icon on top, label below.
 */
function rbBtn(string $url, string $icon, string $label, bool $active = false): string {
    $cls = $active ? ' rb-active' : '';
    $lbl = htmlspecialchars($label);
    return "<a href=\"{$url}\" class=\"rb-btn{$cls}\" title=\"{$lbl}\"><i class=\"{$icon}\"></i><span>{$lbl}</span></a>";
}

/**
 * Check if a parsed child URL matches the current request exactly.
 * Every query param defined in the child URL must be present and equal
 * in the current request. Extra params in the current URL are ignored.
 */
function rbUrlMatches(array $cp, string $rbPage, string $rbMod): bool {
    // Build current params map
    $current = [
        'page' => $rbPage,
        'mod'  => $rbMod,
        'sel'  => $_GET['sel']  ?? '',
        'act'  => $_GET['act']  ?? '',
        'type' => $_GET['type'] ?? '',
        'view' => $_GET['view'] ?? '',
    ];

    // Child URL must define at least one meaningful param
    $childParams = array_filter($cp, fn($v) => $v !== '');
    if (empty($childParams)) {
        return false;
    }

    // Every param in the child URL must match the current URL exactly
    foreach ($childParams as $key => $val) {
        if (!isset($current[$key]) || $current[$key] !== $val) {
            return false;
        }
    }

    // If child URL has no 'type', current URL must also have no type (or default 'post')
    if (!isset($childParams['type'])) {
        $curType = $_GET['type'] ?? '';
        if ($curType !== '' && $curType !== 'post') return false;
    }

    // If child URL has no 'mod', current URL must also have no mod
    if (!isset($childParams['mod'])) {
        if ($rbMod !== '') return false;
    }

    // If child URL has no 'act', current URL must also have no act
    if (!isset($childParams['act'])) {
        $curAct = $_GET['act'] ?? '';
        if ($curAct !== '') return false;
    }

    // If child URL has no 'sel', current URL must also have no sel
    if (!isset($childParams['sel'])) {
        $curSel = $_GET['sel'] ?? '';
        if ($curSel !== '') return false;
    }

    return true;
}

/**
 * Render the ribbon panel content for one menu item.
 * - No children  → single large rb-btn for the item itself.
 * - Has children → one large rb-btn per visible child.
 * All buttons are placed in a single rb-group labelled with the item name.
 */
function rbRenderGroup(array $item, string $rbPage, string $rbMod): string {
    $visibleChildren = array_values(array_filter(
        $item['children'] ?? [],
        fn($c) => User::access((string)($c['access'] ?? 6))
    ));

    $html = '<div class="rb-group">';
    $html .= '<div class="rb-group-btns">';

    if (empty($visibleChildren)) {
        // No children — the item itself is the button
        $q = parse_url($item['url'] ?? '', PHP_URL_QUERY) ?? '';
        parse_str($q, $cp);
        $isActive = rbUrlMatches($cp, $rbPage, $rbMod);
        $html .= rbBtn($item['url'], $item['icon'], $item['label'], $isActive);
    } else {
        // Children become the buttons
        foreach ($visibleChildren as $child) {
            $q = parse_url($child['url'] ?? '', PHP_URL_QUERY) ?? '';
            parse_str($q, $cp);
            $isActive = rbUrlMatches($cp, $rbPage, $rbMod);
            $icon = $child['icon'] ?? 'bi bi-circle';
            $html .= rbBtn($child['url'], $icon, $child['label'], $isActive);
        }
    }

    $html .= '</div>';
    $html .= '<span class="rb-group-label">' . htmlspecialchars($item['label']) . '</span>';
    $html .= '</div>';
    return $html;
}
?>

<!-- ── TITLE BAR ──────────────────────────────────── -->
<div id="rb-titlebar">
  <a href="<?= Site::$url . ADMIN_DIR ?>/index.php" class="rb-logo">
    <?= Site::logo('', '20px') ?>
    <span><?= htmlspecialchars(Options::v('site_name') ?: 'GeniXCMS') ?></span>
  </a>
  <span class="rb-sitename"><?= _('Admin Panel') ?> &mdash; v<?= System::$version ?></span>
  <div class="rb-title-actions">
    <?php echo Hooks::run('admin_header_top_right_action'); ?>
    <a href="<?= Site::$url ?>" target="_blank" class="rb-title-btn">
      <i class="bi bi-eye"></i>
      <span><?= _('Visit Site') ?></span>
    </a>
    <div class="rb-user-wrap">
      <button class="rb-title-btn" onclick="this.closest('.rb-user-wrap').classList.toggle('open')">
        <i class="bi bi-person-circle"></i>
        <span><?= htmlspecialchars((string)Session::val('username')) ?></span>
        <i class="bi bi-chevron-down" style="font-size:.55rem;opacity:.7"></i>
      </button>
      <div class="rb-user-menu">
        <div class="rb-um-head">
          <div class="rb-um-name"><?= htmlspecialchars((string)Session::val('username')) ?></div>
          <div class="rb-um-role"><?= _('Administrator') ?></div>
        </div>
        <a href="index.php?page=users&act=edit&id=<?= User::id(Session::val('username')) ?>&token=<?= TOKEN ?>">
          <i class="bi bi-person"></i> <?= _('My Profile') ?>
        </a>
        <a href="index.php?page=settings">
          <i class="bi bi-gear"></i> <?= _('Settings') ?>
        </a>
        <hr>
        <a href="<?= Url::logout() ?>" class="rb-danger">
          <i class="bi bi-power"></i> <?= _('Logout') ?>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ── TAB BAR ────────────────────────────────────── -->
<div id="rb-tabbar">
  <!-- Fixed Home tab -->
  <a href="index.php?_rtab=home" class="rb-tab <?= $_rb_tab === 'home' ? 'active' : '' ?>">
    <i class="bi bi-house-door"></i> <?= _('Home') ?>
  </a>
  <!-- One tab per menu item -->
  <?php foreach ($_rb_all_items as $item): ?>
  <a href="index.php?_rtab=<?= urlencode($item['id']) ?>"
     class="rb-tab <?= $_rb_tab === $item['id'] ? 'active' : '' ?>">
    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
    <?= htmlspecialchars($item['label']) ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- ── RIBBON PANEL ───────────────────────────────── -->
<div id="rb-panel">

  <!-- HOME panel: dashboard + quick actions -->
  <div class="rb-panel-content <?= $_rb_tab === 'home' ? 'active' : '' ?>" data-tab="home">
    <div class="rb-group">
      <div class="rb-group-btns">
        <?= rbBtn('index.php', 'bi bi-speedometer2', _('Dashboard'), !isset($_GET['page']) && $_rb_tab === 'home') ?>
      </div>
      <span class="rb-group-label"><?= _('Overview') ?></span>
    </div>
    <div class="rb-sep"></div>
    <div class="rb-group">
      <div class="rb-group-btns">
        <?= rbBtn('index.php?page=posts&act=add&type=post',  'bi bi-file-earmark-plus', _('New Post')) ?>
        <?= rbBtn('index.php?page=pages&act=add&type=page',  'bi bi-journal-plus',      _('New Page')) ?>
        <?= rbBtn('index.php?page=media',                    'bi bi-images',            _('Media')) ?>
      </div>
      <span class="rb-group-label"><?= _('Quick Create') ?></span>
    </div>
    <div class="rb-sep"></div>
    <div class="rb-group">
      <div class="rb-group-btns">
        <?= rbBtn('index.php?page=updates', 'bi bi-arrow-repeat',  _('Updates')) ?>
        <?= rbBtn('index.php?page=health',  'bi bi-shield-check',  _('Health')) ?>
        <?= rbBtn(Site::$url,               'bi bi-box-arrow-up-right', _('Site'), false) ?>
      </div>
      <span class="rb-group-label"><?= _('System') ?></span>
    </div>
    <?php echo Hooks::run('admin_ribbon_home'); ?>
  </div>

  <!-- Dynamic panel per menu item -->
  <?php foreach ($_rb_all_items as $item): ?>
  <div class="rb-panel-content <?= $_rb_tab === $item['id'] ? 'active' : '' ?>"
       data-tab="<?= htmlspecialchars($item['id']) ?>">
    <?= rbRenderGroup($item, $_rb_page, $_rb_mod) ?>
    <?php echo Hooks::run('admin_ribbon_tab_' . $item['id']); ?>
  </div>
  <?php endforeach; ?>

</div><!-- /#rb-panel -->

<!-- ── MAIN CONTENT ───────────────────────────────── -->
<div id="rb-main">