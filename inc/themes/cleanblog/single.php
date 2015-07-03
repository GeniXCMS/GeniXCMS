<!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
    <header class="intro-header" style="background-image: url('<?=Site::$url;?>/inc/themes/cleanblog/img/post-bg.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="post-heading">
                        <h1><?=$data['posts'][0]->title;?></h1>
                        <!-- <h2 class="subheading"></h2> -->
                        <span class="meta">Posted by <a href="#"><?=$data['posts'][0]->author;?></a> on <?=Date::local($data['posts'][0]->date);?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Post Content -->
    <article>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <?php
                    if(isset($data['posts'][0]->title)){
                        foreach ($data['posts'] as $p) {
                            # code...
                            echo "
                            <div class=\"\">
                                ".Posts::content($p->content)."
                            </div>
                            <hr />
                            <div class=\"col-sm-12\">
                                <div class=\"row\">
                                <h3>Comments</h3>
                                <div class=\"fb-comments\" data-href=\"http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\" data-width=\"100%\" data-numposts=\"5\" data-colorscheme=\"light\"></div>
                                </div>
                            </div>
                                ";
                        }
                    }else{
                        //echo "Error, Post not found."; 
                        Control::error('404');
                    }
                    
                    
                ?>
                </div>
            </div>
        </div>
    </article>
