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
  <?php
  if(isset($_GET['page'])) { $page = $_GET['page']; } else { $page = "";}
  switch ($page) {
    case 'dashboard':
      # code...
      break;
    
    case 'reports':
      $postactive = "";
      $pageactive = "";
      $useractive = "";
      $menuactive = "";
      $themeactive = "";
      $catactive = "";
      $reportactive = "";
      $postactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;

    case 'posts':
      $overviewactive = "";
      $pageactive = "";
      $useractive = "";
      $menuactive = "";
      $themeactive = "";
      $catactive = "";
      $reportactive = "";
      $postactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;
    case 'categories':
      $overviewactive = "";
      $postactive = "";
      $pageactive = "";
      $useractive = "";
      $menuactive = "";
      $themeactive = "";
      $reportactive = "";
      $catactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;
    case 'pages':
     $overviewactive = "";
      $postactive = "";
      $catactive = "";
      $useractive = "";
      $menuactive = "";
      $themeactive = "";
      $reportactive = "";
      $pageactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;
    case 'users':
      $overviewactive = "";
      $postactive = "";
      $catactive = "";
      $menuactive = "";
      $themeactive = "";
      $reportactive = "";
      $pageactive = "";
      $useractive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;
    case 'menus':
      $overviewactive = "";
      $postactive = "";
      $catactive = "";
      $useractive = "";
      $themeactive = "";
      $reportactive = "";
      $pageactive = "";
      $menuactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;
    case 'themes':
      $overviewactive = "";
      $postactive = "";
      $catactive = "";
      $useractive = "";
      $menuactive = "";
      $reportactive = "";
      $pageactive = "";
      $themeactive = "class='active'";
      $modactive = "";
      $dashboard = "";
      break;


      case 'mods':
      $overviewactive = "";
      $postactive = "";
      $catactive = "";
      $useractive = "";
      $menuactive = "";
      $reportactive = "";
      $pageactive = "";
      $themeactive = "";
      $modactive = "class='active'";
      $dashboard = "";
      break;
    
    default:
      # code...
      $overviewactive = "";
      $postactive = "";
      $pageactive = "";
      $useractive = "";
      $catactive = "";
      $menuactive = "";
      $themeactive = "";
      $reportactive = "";
      $modactive = "";
      $dashboard = "class='active'";
      break;
  }
?>
  <body>
    <div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><span class="mg genixcms-logo"></span>GeniXCMS</a>
        </div>


          <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse collapse">

                <ul class="nav" id="side-menu">
                  
                  <li <?=$dashboard;?>><a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                  <li <?=$postactive;?>><a href="index.php?page=posts"><i class="fa fa-file-text-o"></i> Posts</a></li>
                  <li <?=$catactive;?>><a href="index.php?page=categories"><i class="fa fa-cubes"></i> Categories</a></li>
                  <li <?=$pageactive;?>><a href="index.php?page=pages"><i class="fa fa-file-o"></i> Pages</a></li>
                  <li <?=$useractive;?>><a href="index.php?page=users"><i class="fa fa-users"></i> Users</a></li>
                  <li <?=$menuactive;?>><a href="index.php?page=menus"><i class="fa fa-sitemap"></i> Menus</a></li>
                  <li <?=$menuactive;?>><a href="index.php?page=settings"><i class="fa fa-wrench"></i> Settings</a></li>
                  <li <?=$menuactive;?>><a href="logout.php"><i class="fa fa-power-off"></i> Log Out</a></li>
                  <!-- <li <?=$themeactive;?>><a href="index.php?page=themes">Themes</a></li> -->
                  <!-- <li <?=$modactive;?>><a href="index.php?page=mods">Modules</a></li> -->
                </ul>
                <?php //echo Mod::ModMenu();?>
            </div>
          </div>
    </nav>


    <div id="page-wrapper">
