<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        Site::meta();
    ?>

    
    <meta name="google-site-verification" content="hupql7CIZZqF2OrTKdXQJMJRMCqOY3sZlA6Vwh3u0Yg" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="blog-masthead">
      <div class="container">
       <!--  <nav class="blog-nav">
          <a class="blog-nav-item active" href="#">Home</a>
          <a class="blog-nav-item" href="#">New features</a>
          <a class="blog-nav-item" href="#">Press</a>
          <a class="blog-nav-item" href="#">New hires</a>
          <a class="blog-nav-item" href="#">About</a>
        </nav> -->
        <nav class="navbar" role="navigation">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?=GX_URL;?>"><img src="<?=GX_URL.Options::get('logo');?>" class="logo"><?=Options::get('sitename');?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <?php
             
              echo Menus::getMenu('mainmenu', 'nav navbar-nav navbar-right', true);
            ?>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
      </div>
    </div>

    <div class="container">

      <div class="blog-header">
      </div>

      <div class="row">
