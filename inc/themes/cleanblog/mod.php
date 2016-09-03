    <header class="intro-header" style="background-image: url('<?=Site::$url;?>/inc/themes/cleanblog/img/about-bg.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="page-heading">
                        <h1><?=$data['mod'];?></h1>
                        <hr class="small">
                        <!-- <span class="subheading"></span> -->
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="container">
        <div class="row">
            
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <?php
                    Hooks::run('mod_control', $data);
                    ?>
                </div>
                

        </div>
    </section>
