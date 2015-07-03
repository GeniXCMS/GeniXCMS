<!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
    <header class="intro-header" style="background-image: url('<?=Site::$url;?>/inc/themes/cleanblog/img/home-bg.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="site-heading">
                        <h1><?=Site::$name;?></h1>
                        <hr class="small">
                        <span class="subheading"><?=Options::get('siteslogan');?></span>
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
                if($data['num'] > 0) {
                    foreach ($data['posts'] as $p) {
                        # code...
                        echo "
                        <div class=\"post-preview\">
                            <h2 class=\"post-title\"><a href=\"".Url::post($p->id)."\">$p->title</a></h2>
                            
                            <p class=\"post-subtitle\">".Posts::format($p->content, $p->id)."</p>
                            <p class=\"post-meta\">".Date::local($p->date)." by <a href=\"#\">{$p->author}</a></p>
                        </div>
                            ";
                    }
                    echo $data['paging'];
                }else{
                    echo "No Post to Show";
                }

            ?>
            </div>
        </div>
