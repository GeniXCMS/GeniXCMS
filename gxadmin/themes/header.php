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
                <?=Site::logo('','50px');?>
                <?=Site::$name;?>
            </a>
        </div>

        <?php if( User::access(2) ) { ?>
          <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse collapse">

                <ul class="nav" id="side-menu">
                  
                  <li><a href="index.php"><i class="fa fa-dashboard fa-fw"></i> <?=DASHBOARD;?></a></li>
                  <li ><a href="index.php?page=posts"><i class="fa fa-file-text-o"></i> <?=POSTS;?></a></li>
                  <li ><a href="index.php?page=categories"><i class="fa fa-cubes"></i> <?=CATEGORIES;?></a></li>
                  <li ><a href="index.php?page=pages"><i class="fa fa-file-o"></i> <?=PAGES;?></a></li>
                  <li ><a href="index.php?page=users"><i class="fa fa-users"></i> <?=USERS;?></a></li>
                  <li><a href="index.php?page=menus"><i class="fa fa-sitemap"></i> <?=MENUS;?></a></li>
                  <li><a href="index.php?page=themes"><i class="fa fa-paint-brush"></i> <?=THEMES;?></a></li>
                  <li><a href="index.php?page=modules"><i class="fa fa-plug"></i> <?=MODULES;?></a></li>
                  <li ><a href="index.php?page=settings"><i class="fa fa-wrench"></i> <?=SETTINGS;?></a></li>
                  <?php echo Theme::thmMenu();?>
                  <?php echo Mod::ModMenu();?>
                  <li><a href="logout.php"><i class="fa fa-power-off"></i> <?=LOGOUT;?></a></li>
                </ul>
                
            </div>
          </div>
          <?php }else {} ?>
    </nav>


    <div id="page-wrapper">
