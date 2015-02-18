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
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Menu</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">
                <img src="<?=Options::get('siteurl').Options::get('logo');?>" style="border: 0; max-height: 49px;"> 
                <?=Options::get('sitename');?>
            </a>
        </div>


          <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse collapse">

                <ul class="nav" id="side-menu">
                  
                  <li><a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                  <li ><a href="index.php?page=posts"><i class="fa fa-file-text-o"></i> Posts</a></li>
                  <li ><a href="index.php?page=categories"><i class="fa fa-cubes"></i> Categories</a></li>
                  <li ><a href="index.php?page=pages"><i class="fa fa-file-o"></i> Pages</a></li>
                  <li ><a href="index.php?page=users"><i class="fa fa-users"></i> Users</a></li>
                  <li><a href="index.php?page=menus"><i class="fa fa-sitemap"></i> Menus</a></li>
                  <li ><a href="index.php?page=settings"><i class="fa fa-wrench"></i> Settings</a></li>
                  <?php echo Mod::ModMenu();?>
                  <li><a href="logout.php"><i class="fa fa-power-off"></i> Log Out</a></li>
                </ul>
                
            </div>
          </div>
    </nav>


    <div id="page-wrapper">
