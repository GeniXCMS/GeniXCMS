<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?=Site::meta('backend');?>
    <!-- LOAD CSS -->
    <link href="<?=Site::$url;?>assets/css/bootstrap.min.css" rel="stylesheet">
<!--    <link href="--><?//=Site::$url;?><!--assets/css/bootstrap-theme.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/grideditor.css" rel="stylesheet">

    <!-- Custom CSS -->
<!--    <link href="--><?//=Site::$url;?><!--assets/css/sb-admin-2.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/dashboard.css" rel="stylesheet">
<!--    <link href="--><?//=Site::$url;?><!--assets/css/genixfont.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/flag-icon.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/jquery.tagsinput.min.css" rel="stylesheet">



    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/iCheck/square/blue.css">
    <!-- Morris chart -->
<!--    <link rel="stylesheet" href="--><?//=Site::$url;?><!--assets/plugins/morris/morris.css">-->
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/daterangepicker/daterangepicker.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- bootstrap slider -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/bootstrap-slider/slider.css">

    <!-- LOAD Javascript -->
    <script src="<?=Site::$url;?>assets/js/jquery.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/bootstrap.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/moment-locales.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.tagsinput.min.js"></script>

    <!-- jQuery UI 1.11.4 -->
<!--    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->
    <script src="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.structure.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <!-- Metis Menu Plugin JavaScript -->
<!--    <script src="--><?//=Site::$url;?><!--assets/js/plugins/metisMenu/metisMenu.min.js"></script>-->

    <!-- Custom Theme JavaScript -->
    <!--    <script src="--><?//=Site::$url;?><!--assets/js/sb-admin-2.js"></script>-->

    <!-- MetisMenu CSS -->
    <!--    <link href="--><?//=Site::$url;?><!--assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">-->

    <link href="<?=Site::$url;?>assets/css/summernote.css" rel="stylesheet">
    <script src="<?=Site::$url;?>assets/js/summernote.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-ext-genixcms.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-image-attributes.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-floats-bs.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.grideditor.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.grideditor.summernote.js"></script>
    <script src="<?=Site::$url;?>assets/js/genixcms.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-black sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini img-responsive"><?=Site::logo('', '28px');?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg img-responsive">
                <?=Site::logo('', '33px');?>
            </span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?=Site::$url;?>assets/images/user1-256x256.png" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?=Session::val('username');?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?=Site::$url;?>assets/images/user1-256x256.png" class="img-circle" alt="User Image">

                                <p>
                                    <?=Session::val('username');?>
                                    <small>Member since <?=Date::format(User::regdate(Session::val('username')));?></small>
                                </p>
                            </li>
                            <!-- Menu Body -->
<!--                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </div>
                                <!-- /.row -->
<!--                            </li>
                             Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="index.php?page=users&act=edit&id=<?=User::id(Session::val('username'));?>&token=<?=TOKEN;?>" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="<?=Site::$url;?>" target="_blank"><i class="fa fa-globe"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?=Site::$url;?>assets/images/user1-256x256.png" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?=Session::val('username');?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                <li class="<?=(!isset($_GET['page'])) ? 'active' : ''; ?>">
                    <a href="index.php">
                        <i class="fa fa-dashboard"></i> <span><?=DASHBOARD; ?></span>
                    </a>
                </li>
                <li class="<?=(isset($_GET['page']) && ($_GET['page'] == 'posts' || $_GET['page'] == 'categories' || $_GET['page'] == 'tags')) ? 'active' : ''; ?> treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span><?=POSTS; ?></span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'posts') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=posts"><i class="fa fa-pencil fa-fw"></i> <?=POSTS;?></a>
                        </li>
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'categories') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=categories"><i class="fa fa-folder fa-fw"></i> <?=CATEGORIES; ?></a>
                        </li>
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'tags') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=tags"><i class="fa fa-tags fa-fw"></i> <?=TAGS; ?></a>
                        </li>
                    </ul>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'pages') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=pages">
                        <i class="fa fa-file-o"></i> <span><?=PAGES; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'comments') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=comments">
                        <i class="fa fa-comments fa-fw"></i> <span><?=COMMENTS; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'media') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=media">
                        <i class="fa fa-photo"></i> <span>Media</span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'users') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=users">
                        <i class="fa fa-users"></i> <span><?=USERS; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'menus') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=menus">
                        <i class="fa fa-sitemap"></i> <span><?=MENUS; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'themes' && !isset($_GET['view'])) ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=themes">
                        <i class="fa fa-paint-brush"></i> <span><?=THEMES; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && $_GET['page'] == 'modules') ? 'class="active"' : ''; ?>>
                    <a href="index.php?page=modules">
                        <i class="fa fa-plug"></i> <span><?=MODULES; ?></span>
                    </a>
                </li>
                <li <?=(isset($_GET['page']) && ($_GET['page'] == 'multilang' || $_GET['page'] == 'settings'
                        || $_GET['page'] == 'permalink' || $_GET['page'] == 'comments-settings' || $_GET['page'] == 'cache' )) ? 'class="active treeview"' : 'class="treeview"'; ?> >
                    <a href="#"><i class="fa fa-wrench fa-fw"></i> <span><?=SETTINGS; ?></span>

                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'settings') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=settings">
                                <i class="fa fa-wrench"></i> <span>Global Settings</span>
                            </a>
                        </li>
                        <li  <?=(isset($_GET['page']) && $_GET['page'] == 'multilang') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=multilang">
                                <i class="fa fa-flag"></i> <span>Multilanguage</span>
                            </a>
                        </li>
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'permalink') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=permalink">
                                <i class="fa fa-link"></i> <span>Permalink</span>
                            </a>
                        </li>
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'comments-settings') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=comments-settings">
                                <i class="fa fa-comments"></i> <span>Comments Settings</span>
                            </a>
                        </li>
                        <li <?=(isset($_GET['page']) && $_GET['page'] == 'cache') ? 'class="active"' : ''; ?>>
                            <a href="index.php?page=cache">
                                <i class="fa fa-archive"></i> <span>Cache Settings</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php echo Theme::thmMenu(); ?>
                <?php echo Mod::modMenu(); ?>
                <li>
                    <a href="logout.php">
                        <i class="fa fa-power-off"></i> <span><?=LOGOUT; ?></span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">