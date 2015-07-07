<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=Site::meta();?>

    <!-- Custom styles for this template -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
</head>

<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-nav">
                    <span class="sr-only"><?=MENU;?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">
                    <?=Site::logo('','45px');?>
                </a>
            </div>

            <?php if( User::access(2) ) { ?>
                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">

                        <ul class="nav" id="side-menu">

                            <li>
                                <a href="index.php" <?=(!isset($_GET['page']))?"":"";?>>
                                    <i class="fa fa-dashboard fa-fw"></i> <?=DASHBOARD;?>
                                </a>
                            </li>
                            <li <?=(isset($_GET['page']) && ($_GET['page'] == 'posts' || $_GET['page'] == 'categories'))?"class=\"active\"":"";?> >
                                <a href="#"><i class="fa fa-file-text-o fa-fw"></i> Posts<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level" style="">
                                    <li>
                                        <a href="index.php?page=posts" 
                                        <?=(isset($_GET['page']) && $_GET['page'] == 'posts')?"class=\"active\"":"";?>>
                                            <?=POSTS;?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=categories" 
                                        <?=(isset($_GET['page']) && $_GET['page'] == 'categories')?"class=\"active\"":"";?>>
                                            <?=CATEGORIES;?>
                                        </a>
                                    </li>
                                </ul>
                                
                            </li>
                            <li>
                                <a href="index.php?page=pages" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'pages')?"class=\"active\"":"";?>>
                                    <i class="fa fa-file-o"></i> <?=PAGES;?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=users" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'users')?"class=\"active\"":"";?>>
                                    <i class="fa fa-users"></i> <?=USERS;?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=media" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'media')?"class=\"active\"":"";?>>
                                    <i class="fa fa-file-archive-o"></i> Media
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=menus" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'menus')?"class=\"active\"":"";?>>
                                    <i class="fa fa-sitemap"></i> <?=MENUS;?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=themes" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'themes' && !isset($_GET['view']))?"class=\"active\"":"";?>>
                                    <i class="fa fa-paint-brush"></i> <?=THEMES;?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=modules" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'modules')?"class=\"active\"":"";?>>
                                    <i class="fa fa-plug"></i> <?=MODULES;?>
                                </a></li>
                            <li>
                                <a href="index.php?page=settings" 
                                <?=(isset($_GET['page']) && $_GET['page'] == 'settings')?"class=\"active\"":"";?>>
                                    <i class="fa fa-wrench"></i> <?=SETTINGS;?>
                                </a></li>
                            <?php echo Theme::thmMenu();?>
                            <?php echo Mod::ModMenu();?>
                            <li>
                                <a href="logout.php">
                                    <i class="fa fa-power-off"></i> <?=LOGOUT;?>
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>
                <?php }else {} ?>
            </nav>


            <div id="page-wrapper">
