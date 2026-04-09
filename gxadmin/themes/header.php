<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Site::meta('backend'); ?>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- GeniXCMS Asset Manager -->
    <?= Site::loadLibHeader(); ?>
    <?php echo Hooks::run('admin_header_action'); ?>

    <?php $admin_layout = Options::v('admin_layout_type') ?: 'sidebar'; ?>

    <?php if (Options::v('admin_custom_css')): ?>
        <style>
            <?= Options::v('admin_custom_css'); ?>
        </style>
    <?php endif; ?>
</head>

<body>

    <?php $admin_layout = Options::v('admin_layout_type') ?: 'sidebar'; ?>
    <?php if ($admin_layout === 'sidebar' || $admin_layout === 'top'): ?>
        <div id="sidebar" class="<?= ($admin_layout === 'top') ? 'd-lg-none' : ''; ?>">
            <div class="sidebar-header">
                <a href="<?= Site::$url . ADMIN_DIR; ?>/index.php" class="sidebar-logo">
                    <?= Site::logo('', '30px'); ?>
                </a>
                <button class="btn btn-link text-white d-lg-none" id="sidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <ul class="sidebar-menu">
                <?php Hooks::run('admin_sidebar_start'); ?>

                <li class="menu-label"><?= _("Main Navigation"); ?></li>
                <li class="<?= (!isset($_GET['page'])) ? 'active' : ''; ?>">
                    <a href="index.php"><i class="bi bi-speedometer2"></i> <span><?= _("Dashboard"); ?></span></a>
                </li>
                <?php echo AdminMenu::renderSidebar('main'); ?>

                <li class="menu-label"><?= _("Management"); ?></li>
                <?php echo AdminMenu::renderSidebar('management'); ?>

                <?php echo AdminMenu::renderSidebar('settings'); ?>

                <li class="menu-label"><?= _("Extensions"); ?></li>
                <?php echo AdminMenu::renderSidebar('external'); ?>

                <li>
                    <a href="<?= Url::logout(); ?>" class=" text-danger"><i class="bi bi-power"></i>
                        <span><?= _("Logout"); ?></span></a>
                </li>
                <?php Hooks::run('admin_sidebar_end'); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div id="main-wrapper" class="<?= ($admin_layout === 'top') ? 'expanded' : ''; ?>">
        <header class="top-navbar bg-white border-bottom shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light border me-3 <?= ($admin_layout === 'sidebar') ? '' : 'd-lg-none'; ?>"
                    id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <?php if ($admin_layout === 'top'): ?>
                    <a href="<?= Site::$url . ADMIN_DIR; ?>/index.php"
                        class="sidebar-logo text-dark me-4 d-none d-lg-flex align-items-center text-decoration-none">
                        <?= Site::logo('', '30px'); ?>
                    </a>
                <?php endif; ?>
                <h5 class="mb-0 fw-bold d-none d-xl-block"><?= _("Admin Dashboard"); ?></h5>
            </div>

            <?php if ($admin_layout === 'top'): ?>
                <!-- Horizontal Top Navigation -->
                <nav
                    class="navbar navbar-expand-lg navbar-light bg-transparent py-0 flex-grow-1 ms-3 d-none d-lg-flex top-nav-compact">
                    <ul class="navbar-nav me-auto mb-0 align-items-center" style="gap: 2px;">
                        <li class="nav-item">
                            <a class="nav-link px-2 py-1 rounded <?= (!isset($_GET['page'])) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>"
                                href="index.php">
                                <i class="bi bi-speedometer2"></i> <?= _("Dashboard"); ?>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-2 py-1 rounded text-secondary font-weight-medium hover-bg-light"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-pencil-square"></i> <?= _("Content"); ?>
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                                <?php echo AdminMenu::renderTopNav('main', true); ?>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-2 py-1 rounded text-secondary font-weight-medium hover-bg-light"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-gear"></i> <?= _("Management"); ?>
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                                <?php echo AdminMenu::renderTopNav('management', true); ?>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-2 py-1 rounded text-secondary font-weight-medium hover-bg-light"
                                href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-gear-wide-connected"></i> <?= _("Settings"); ?>
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                                <?php echo AdminMenu::renderTopNav('settings', true); ?>
                            </ul>
                        </li>

                        <?php
                        $externalPlugMenu = AdminMenu::renderTopNav('external');
                        if (!empty(strip_tags($externalModMenu)) || !empty($externalPlugMenu)):
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-2 py-1 rounded text-secondary font-weight-medium hover-bg-light"
                                    href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-puzzle"></i> <?= _("Extensions"); ?>
                                </a>
                                <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3 top-external-menu">
                                    <?php echo $externalPlugMenu; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <style>
                :root {
                    --gx-header-height:
                        <?php echo ($admin_layout === 'top') ? '60px' : '56px'; ?>
                    ;
                }

                .top-nav-compact .nav-link {
                    font-size: 0.85rem;
                    letter-spacing: -0.01em;
                }

                .top-nav-compact .nav-link i {
                    font-size: 1rem;
                    margin-right: 3px;
                    vertical-align: middle;
                }

                .top-nav-compact .dropdown-menu {
                    font-size: 0.85rem;
                    min-width: 200px;
                }

                .top-nav-compact .dropdown-item i {
                    font-size: 0.95rem;
                    width: 1.25rem;
                    display: inline-block;
                    text-align: center;
                }

                /* Multi-level dropdowns */
                .top-nav-compact .dropdown-menu .dropdown {
                    position: relative;
                }

                .top-nav-compact .dropdown-menu .dropdown-menu {
                    top: 0;
                    left: 100%;
                    margin-top: -1px;
                }

                .top-nav-compact .dropdown-menu .dropdown:hover>.dropdown-menu {
                    display: block;
                }

                .top-nav-compact .dropdown-menu .dropdown-toggle::after {
                    transform: rotate(-90deg);
                    vertical-align: middle;
                    margin-left: auto;
                    float: right;
                    margin-top: 5px;
                }

                .top-external-menu>li>a {
                    padding: 0.5rem 1rem;
                    color: #212529;
                    text-decoration: none;
                    display: block;
                }

                .top-external-menu>li>a:hover {
                    background-color: #f8f9fa;
                }

                .top-external-menu>li>a>i {
                    margin-right: 0.5rem;
                    color: #6c757d;
                }
            </style>

            <div class="d-flex align-items-center gap-3">
                <a href="<?= Site::$url; ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill"
                    title="<?= _("View Site"); ?>">
                    <i class="bi bi-eye me-1"></i> <span class="d-none d-sm-inline"><?= _("Visit Site"); ?></span>
                </a>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                        data-bs-toggle="dropdown">
                        <img src="<?= Site::$url; ?>assets/images/user1-256x256.png" class="rounded-circle me-2"
                            width="32" height="32">
                        <span class="fw-semibold d-none d-sm-inline"><?= Session::val('username'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small text-muted"><?= _("Signed in as"); ?></div>
                            <div class="fw-bold"><?= Session::val('username'); ?></div>
                        </li>
                        <li><a class="dropdown-item mt-1"
                                href="index.php?page=users&act=edit&id=<?= User::id(Session::val('username')); ?>&token=<?= TOKEN; ?>"><i
                                    class="bi bi-person me-2"></i> <?= _("Profile"); ?></a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= Url::logout(); ?>"><i
                                    class=" bi bi-power me-2"></i> <?= _("Logout"); ?></a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-body">