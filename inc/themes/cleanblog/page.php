 <!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
    <header class="intro-header" style="background-image: url('<?=Site::$url;?>/inc/themes/cleanblog/img/about-bg.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="page-heading">
                        <h1><?=$data['posts'][0]->title;?></h1>
                        <hr class="small">
                        <!-- <span class="subheading"></span> -->
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
            <?php
                foreach ($data['posts'] as $p) {
                    # code...
                    echo "
                    <div class=\"blog-post\">
                        <h2 class=\"blog-post-title\">$p->title</h2>
                       
                       ".Typo::Xclean($p->content)."
                    </div>
                        ";
                }
            ?>
            </div>
        </div>
    </div>
