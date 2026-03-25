<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    Site::meta();
    ?>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Gloock&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>
</head>

<body>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=422479467810457";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        <div class="blog-masthead">
            <div class="container">
        <!--  <nav class="blog-nav">
          <a class="blog-nav-item active" href="#">Home</a>
          <a class="blog-nav-item" href="#">New features</a>
          <a class="blog-nav-item" href="#">Press</a>
          <a class="blog-nav-item" href="#">New hires</a>
          <a class="blog-nav-item" href="#">About</a>
        </nav> -->
        <nav class="navbar navbar-expand-lg bg-body-transparent">
			  <div class="container-fluid">
				<a class="navbar-brand" href="<?=Site::$url;?>"><?=Site::logo(height:'30px');?></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
				  <span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarToggler">
                <?php
                    echo Menus::getMenu('mainmenu', 'nav navbar-nav  me-auto mb-2 mb-lg-0', true);
                ?>
				</div>
			  </div>
			</nav>
        
    </div>
</div>

<div class="container">

    <div class="blog-header">
        <h1 class="blog-title"><a href="<?=Site::$url;?>"><?=Site::$name;?></a></h1>
        <p class="lead blog-description"><?=Options::v('siteslogan');?></p>
    </div>

    <div class="row">
