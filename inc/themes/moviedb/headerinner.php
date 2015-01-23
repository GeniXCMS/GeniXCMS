<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        Site::meta('','','Watch Online ');
    ?>
    <meta name="google-site-verification" content="hupql7CIZZqF2OrTKdXQJMJRMCqOY3sZlA6Vwh3u0Yg" />
    <meta property="og:title" content="<?=$data['posts'][0]->title;?>">
    <meta property="og:site_name" content="<?=Options::get('sitename');?>">
    <meta property="og:type" content="movie">
    <meta property="fb:admins" content="1337">
    <meta property="og:image" content="<?=$data['posts'][0]->backdrop;?>"/>
    <meta property="og:url" content="<?=Url::post($_GET['post']); ?>" />
    <meta property="og:description" content="<?=$data['posts'][0]->content;?>" />
    
    <script src="http://releases.flowplayer.org/js/flowplayer-3.2.12.min.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body class="innerpage">

    
    <div class="container">

      <div class="blog-header clearfix">
        <div class="col-md-6">
          <h1 class="">
          <img src="<?=GX_URL.Options::get('logo');?>" class="logo">
          <a href="<?=GX_URL;?>"><?=Options::get('sitename');?></a></h1>
        </div>
        
        <div class="col-md-6">
          <a href="<?=GX_URL;?>/signup.php" target="_blank" class="btn btn-danger pull-right">Register Now</a>
        </div>
      </div>

      <div class="row">
