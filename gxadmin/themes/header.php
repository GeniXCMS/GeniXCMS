<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=Site::meta('backend');?>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet">
    <?php $__editorType = Options::v('editor_type') ?: 'summernote'; ?>
    <?php if ($__editorType === 'summernote'): ?>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
    <?php endif; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" rel="stylesheet">
    
    <!-- Custom Admin Style -->
    <link href="<?=Site::$url;?>assets/css/gneex-admin.css" rel="stylesheet">
    <?php $admin_layout = Options::v('admin_layout_type') ?: 'sidebar'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <?php if ($__editorType === 'summernote'): ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
    <?php elseif ($__editorType === 'editorjs'): ?>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.6/dist/editorjs.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.7/dist/header.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.9/dist/editorjs-list.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@2.10.1/dist/image.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@2.7.6/dist/quote.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/code@2.9.3/dist/code.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.6/dist/embed.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@2.4.3/dist/table.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.3.0/dist/bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/inline-code@1.5.1/dist/inline-code.umd.min.js"></script>
    <?php endif; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"></script>
    <script src="<?=Site::$url;?>assets/js/genixcms.js"></script>

    <script>
        $(function() {
            toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right" };
        });
    </script>
    <?php if (Options::v('admin_custom_css')): ?>
    <style>
        <?=Options::v('admin_custom_css');?>
    </style>
    <?php endif; ?>
</head>
<body>

<?php $admin_layout = Options::v('admin_layout_type') ?: 'sidebar'; ?>
<?php if ($admin_layout === 'sidebar'): ?>
<div id="sidebar">
    <div class="sidebar-header">
        <a href="<?=Site::$url.ADMIN_DIR;?>/index.php" class="sidebar-logo">
            <?=Site::logo('', '30px');?>
        </a>
        <button class="btn btn-link text-white d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-label"><?=_("Main Navigation");?></li>
        <li class="<?=(!isset($_GET['page'])) ? 'active' : ''; ?>">
            <a href="index.php"><i class="bi bi-speedometer2"></i> <span><?=_("Dashboard");?></span></a>
        </li>

        <?php if(User::access(4)): ?>
        <li class="nav-item <?=(isset($_GET['page']) && in_array($_GET['page'], ['posts', 'categories', 'tags'])) ? 'open active' : ''; ?>">
            <a href="#" class="has-tree"><i class="bi bi-file-earmark-richtext"></i> <span><?=_("Posts");?></span> <i class="bi bi-chevron-down ms-auto small"></i></a>
            <ul class="nav-tree">
                <li><a href="index.php?page=posts" class="<?=(isset($_GET['page']) && $_GET['page'] == 'posts') ? 'text-white' : ''; ?>"><?=_("All Posts");?></a></li>
                <?php if(User::access(1)): ?>
                <li><a href="index.php?page=categories" class="<?=(isset($_GET['page']) && $_GET['page'] == 'categories') ? 'text-white' : ''; ?>"><?=_("Categories");?></a></li>
                <li><a href="index.php?page=tags" class="<?=(isset($_GET['page']) && $_GET['page'] == 'tags') ? 'text-white' : ''; ?>"><?=_("Tags");?></a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if(User::access(1)): ?>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'pages') ? 'active' : ''; ?>">
            <a href="index.php?page=pages"><i class="bi bi-journal-text"></i> <span><?=_("Pages");?></span></a>
        </li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'comments') ? 'active' : ''; ?>">
            <a href="index.php?page=comments"><i class="bi bi-chat-left-dots"></i> <span><?=_("Comments");?></span></a>
        </li>
        <?php endif; ?>

        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'media') ? 'active' : ''; ?>">
            <a href="index.php?page=media"><i class="bi bi-images"></i> <span><?=_("Media");?></span></a>
        </li>

        <?php if(User::access(1)): ?>
        <li class="menu-label"><?=_("Management");?></li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'users') ? 'active' : ''; ?>">
            <a href="index.php?page=users"><i class="bi bi-people"></i> <span><?=_("Users");?></span></a>
        </li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'permissions') ? 'active' : ''; ?>">
            <a href="index.php?page=permissions"><i class="bi bi-shield-lock"></i> <span><?=_("ACL Manager");?></span></a>
        </li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'menus') ? 'active' : ''; ?>">
            <a href="index.php?page=menus"><i class="bi bi-list-nested"></i> <span><?=_("Menus");?></span></a>
        </li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'widgets') ? 'active' : ''; ?>">
            <a href="index.php?page=widgets"><i class="bi bi-grid-1x2"></i> <span><?=_("Widgets");?></span></a>
        </li>
        <?php endif; ?>

        <?php if(User::access(0)): ?>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'themes') ? 'active' : ''; ?>">
            <a href="index.php?page=themes"><i class="bi bi-palette"></i> <span><?=_("Themes");?></span></a>
        </li>
        <li class="<?=(isset($_GET['page']) && $_GET['page'] == 'modules') ? 'active' : ''; ?>">
            <a href="index.php?page=modules"><i class="bi bi-plugin"></i> <span><?=_("Modules");?></span></a>
        </li>
        <?php endif; ?>

        <?php if(User::access(1)): ?>
        <li class="nav-item <?=(isset($_GET['page']) && strpos($_GET['page'], 'settings') !== false) ? 'open active' : ''; ?>">
            <a href="#" class="has-tree"><i class="bi bi-gear"></i> <span><?=_("Settings");?></span> <i class="bi bi-chevron-down ms-auto small"></i></a>
            <ul class="nav-tree">
                <li><a href="index.php?page=settings" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings') ? 'text-white fw-bold' : ''; ?>"><?=_("Global Settings");?></a></li>
                <?php if(User::access(0)): ?>
                <li><a href="index.php?page=settings-media" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings-media') ? 'text-white fw-bold' : ''; ?>"><?=_("Media Settings");?></a></li>
                <li><a href="index.php?page=settings-multilang" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings-multilang') ? 'text-white fw-bold' : ''; ?>"><?=_("Multilanguage Settings");?></a></li>
                <li><a href="index.php?page=settings-permalink" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings-permalink') ? 'text-white fw-bold' : ''; ?>"><?=_("Permalink Settings");?></a></li>
                <?php endif; ?>
                <li><a href="index.php?page=settings-comments" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings-comments') ? 'text-white fw-bold' : ''; ?>"><?=_("Comments Settings");?></a></li>
                <li><a href="index.php?page=settings-cache" class="<?=(isset($_GET['page']) && $_GET['page'] == 'settings-cache') ? 'text-white fw-bold' : ''; ?>"><?=_("Cache Settings");?></a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <li class="menu-label"><?=_("External");?></li>
        <?php echo Theme::thmMenu(); ?>
        <?php echo Mod::modMenu(); ?>

        <li>
            <a href="<?=Site::$url;?>logout/" class="text-danger"><i class="bi bi-power"></i> <span><?=_("Logout");?></span></a>
        </li>
    </ul>
</div>
<?php endif; ?>

<div id="main-wrapper" class="<?= ($admin_layout === 'top') ? 'expanded' : ''; ?>">
    <header class="top-navbar bg-white border-bottom shadow-sm">
        <div class="d-flex align-items-center">
            <?php if ($admin_layout === 'sidebar'): ?>
            <button class="btn btn-light border me-3" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <?php else: ?>
            <a href="<?=Site::$url.ADMIN_DIR;?>/index.php" class="sidebar-logo text-dark me-4 d-flex align-items-center text-decoration-none" style="font-size: 1.25rem;">
                <span class="d-inline-flex bg-primary text-white rounded me-2 align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                    <i class="bi bi-cpu h5 mb-0"></i>
                </span>
                <strong class="tracking-wide">GeniXCMS</strong>
            </a>
            <?php endif; ?>
            <h5 class="mb-0 fw-bold d-none d-xl-block"><?=_("Admin Dashboard");?></h5>
        </div>

        <?php if ($admin_layout === 'top'): ?>
        <!-- Horizontal Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-0 flex-grow-1 ms-3 d-none d-lg-flex">
            <ul class="navbar-nav me-auto mb-0 align-items-center" style="gap: 5px;">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded <?= (!isset($_GET['page'])) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="index.php"><i class="bi bi-speedometer2 me-1"></i> <?=_("Dashboard");?></a>
                </li>
                <?php if(User::access(4)): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded <?= (isset($_GET['page']) && in_array($_GET['page'], ['posts', 'categories', 'tags'])) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="#" data-bs-toggle="dropdown"><i class="bi bi-file-earmark-richtext me-1"></i> <?=_("Articles");?></a>
                    <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                        <li><a class="dropdown-item py-2" href="index.php?page=posts"><i class="bi bi-list-ul me-2 text-muted"></i><?=_("All Posts");?></a></li>
                        <?php if(User::access(1)): ?>
                        <li><a class="dropdown-item py-2" href="index.php?page=categories"><i class="bi bi-folder2 me-2 text-muted"></i><?=_("Categories");?></a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=tags"><i class="bi bi-tags me-2 text-muted"></i><?=_("Tags");?></a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(User::access(1)): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded <?= (isset($_GET['page']) && $_GET['page'] == 'pages') ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="index.php?page=pages"><i class="bi bi-journal-text me-1"></i> <?=_("Pages");?></a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded <?= (isset($_GET['page']) && $_GET['page'] == 'media') ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="index.php?page=media"><i class="bi bi-images me-1"></i> <?=_("Media");?></a>
                </li>
                <?php if(User::access(1)): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded <?= (isset($_GET['page']) && in_array($_GET['page'], ['users', 'permissions', 'menus', 'widgets', 'themes', 'modules'])) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="#" data-bs-toggle="dropdown"><i class="bi bi-sliders me-1"></i> <?=_("Manage");?></a>
                    <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                        <li><h6 class="dropdown-header text-uppercase text-muted fw-bold" style="font-size:0.65rem;"><?=_("Infrastructure");?></h6></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=users"><i class="bi bi-people me-2 text-muted"></i><?=_("Users");?></a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=permissions"><i class="bi bi-shield-lock me-2 text-muted"></i><?=_("ACL Manager");?></a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=menus"><i class="bi bi-list-nested me-2 text-muted"></i><?=_("Menus");?></a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=widgets"><i class="bi bi-grid-1x2 me-2 text-muted"></i><?=_("Widgets");?></a></li>
                        <?php if(User::access(0)): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header text-uppercase text-muted fw-bold" style="font-size:0.65rem;"><?=_("Extensions");?></h6></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=themes"><i class="bi bi-palette me-2 text-muted"></i><?=_("Themes");?></a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=modules"><i class="bi bi-plugin me-2 text-muted"></i><?=_("Modules");?></a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded <?= (isset($_GET['page']) && strpos($_GET['page'], 'settings') !== false) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-secondary font-weight-medium hover-bg-light'; ?>" href="#" data-bs-toggle="dropdown"><i class="bi bi-gear me-1"></i> <?=_("Settings");?></a>
                    <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3">
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings') ? 'fw-bold' : ''; ?>" href="index.php?page=settings"><i class="bi bi-globe me-2 text-muted"></i><?=_("Global Settings");?></a></li>
                        <?php if(User::access(0)): ?>
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings-media') ? 'fw-bold' : ''; ?>" href="index.php?page=settings-media"><i class="bi bi-camera me-2 text-muted"></i><?=_("Media Settings");?></a></li>
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings-multilang') ? 'fw-bold' : ''; ?>" href="index.php?page=settings-multilang"><i class="bi bi-translate me-2 text-muted"></i><?=_("Multilanguage Settings");?></a></li>
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings-permalink') ? 'fw-bold' : ''; ?>" href="index.php?page=settings-permalink"><i class="bi bi-link-45deg me-2 text-muted"></i><?=_("Permalink Settings");?></a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings-comments') ? 'fw-bold' : ''; ?>" href="index.php?page=settings-comments"><i class="bi bi-chat-text me-2 text-muted"></i><?=_("Comments Settings");?></a></li>
                        <li><a class="dropdown-item py-2 <?= (isset($_GET['page']) && $_GET['page'] == 'settings-cache') ? 'fw-bold' : ''; ?>" href="index.php?page=settings-cache"><i class="bi bi-lightning me-2 text-muted"></i><?=_("Cache Settings");?></a></li>
                    </ul>
                </li>
                <?php endif; // Close check User::access(1) ?>
                
                <?php 
                // External Menus (Check if they have any content first)
                $externalThemeMenu = Theme::thmMenu();
                $externalModMenu = Mod::modMenu();
                if(!empty(strip_tags($externalThemeMenu)) || !empty(strip_tags($externalModMenu))):
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded text-secondary font-weight-medium hover-bg-light" href="#" data-bs-toggle="dropdown"><i class="bi bi-box-arrow-up-right me-1"></i> <?=_("External");?></a>
                    <ul class="dropdown-menu border-0 shadow-sm mt-2 rounded-3 top-external-menu">
                        <?php echo $externalThemeMenu; ?>
                        <?php echo $externalModMenu; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <style>
            :root {
                --gx-header-height: <?php echo ($admin_layout === 'top') ? '72px' : '56px'; ?>;
            }
            .top-external-menu > li > a { padding: 0.5rem 1rem; color: #212529; text-decoration: none; display: block; }
            .top-external-menu > li > a:hover { background-color: #f8f9fa; }
            .top-external-menu > li > a > i { margin-right: 0.5rem; color: #6c757d; }
        </style>

        <div class="d-flex align-items-center gap-3">
            <a href="<?=Site::$url;?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill" title="<?=_("View Site");?>">
                <i class="bi bi-eye me-1"></i> <span class="d-none d-sm-inline"><?=_("Visit Site");?></span>
            </a>
            
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                    <img src="<?=Site::$url;?>assets/images/user1-256x256.png" class="rounded-circle me-2" width="32" height="32">
                    <span class="fw-semibold d-none d-sm-inline"><?=Session::val('username');?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li class="px-3 py-2 border-bottom">
                        <div class="small text-muted"><?=_("Signed in as");?></div>
                        <div class="fw-bold"><?=Session::val('username');?></div>
                    </li>
                    <li><a class="dropdown-item mt-1" href="index.php?page=users&act=edit&id=<?=User::id(Session::val('username'));?>&token=<?=TOKEN;?>"><i class="bi bi-person me-2"></i> <?=_("Profile");?></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?=Site::$url;?>logout/"><i class="bi bi-power me-2"></i> <?=_("Logout");?></a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="content-body">