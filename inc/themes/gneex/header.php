<?php
$gneex = Gneex::$opt;
?>
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
        @import 'https://fonts.googleapis.com/css?family=Montserrat&display=swap';
      </style> 
    </head>

    <body>
        <div id="fb-root"></div>

        <header id="header" class="clearfix">
        <div class="container">
            
            <div class="col-md-2 text-center logo">
                <a class="" href="<?=Site::$url;
?>"><?=Site::logo('', '65px');?></a>
<?=Language::flagList();?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-10">
                <nav class="navbar" role="navigation">
                  <div class="container-fluid">
                      <!-- Brand and toggle get grouped for better mobile display -->
                      <div class="navbar-header">
                          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-header">
                              <span class="sr-only">Toggle navigation</span>
                              <span class="icon-bar"></span>
                              <span class="icon-bar"></span>
                              <span class="icon-bar"></span>
                          </button>
                          
                      </div>

                      <!-- Collect the nav links, forms, and other content for toggling -->
                      <div class="collapse navbar-collapse" id="navbar-collapse-header">
                            <?php
                            echo Menus::getMenu('mainmenu', 'nav navbar-nav navbar-right', true);
                            ?>
                      </div><!-- /.navbar-collapse -->
                  </div><!-- /.container-fluid -->
                </nav>
            </div>
            
        </div>
    </header>

<?php
if (isset($data['p_type']) && $data['p_type'] == "index") {
    # code...
?>
    <section id="frontslide">
        
        <div class="bg-slide">
            
        </div>
        <?php
        if (($gneex['intro_title'] || $gneex['intro_text']) != '') {
            # code...

        ?>
        <div class="container" id="front-text">


                <div class="col-md-7 ">
                <div class="front-textbox">
                    <h2><span><?=nl2br($gneex['intro_title']); ?></span></h2>
                    <hr />
                    <p><span><?=nl2br($gneex['intro_text']); ?></span>
                    </p>
                </div>
                    
                </div>
                <div class="col-md-5 front-image">
                    <?=Gneex::introIg($gneex['intro_image']); ?>
                </div>

        </div>
        <?php
        }
        ?>

    </section>
<?php
} else {
    echo "
    <section id=\"innerslide\">
        
        <div class=\"bg-slide\">
            
        </div>

    </section>";
}
?>