    <?php $gneex = Gneex::$opt;?>
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
if (Gneex::featuredExist()) {
    ?>
    <section id="featured">
        <div class="container ">
            <div class="col-md-12 featured">
            <ul class="list-featured slides">
                <?php
                $feat = explode(',', $gneex['featured_posts']);
                foreach ($feat as $id) {
                    $post = Posts::content(Gneex::getPost($id));
                    $title = Posts::title($id);
                    $img = Gneex::getImage($post);
                    if ($img != '') {
                        $im = '<img src="'.Url::thumb($img, 'large', 300).'" class="featuredimg">';
                    } else {
                        $im = '<img src="'.Url::thumb('assets/images/noimage.png', 'large').'" class="featuredimg">';
                    }

                                echo '<li class="col-sm-3">
                                <a href="'.Url::post($id)."\">
                                {$im}
                                <div class=\"featured-text\">
                                    <h4 >{$title}</h4>
                                </div>
                                </a>
                            </li>";
                } ?>
                
            </ul>
            </div>
        </div>
        
    </section>
<?php
}
?>

    <section id="blog">
        <div class="container">
            <div class="col-md-8">
                <?php
                if ($gneex['front_layout'] == 'magazine') {
                    # code...
                ?>

                <?php
                    $cat = $gneex['panel_1'];
                    $post = Posts::getPostCat($cat, 8); ?>
                
                <div class="panel panel-one">
                    <div class="panel-heading">
                    <h3 class="panel-title"><?=Categories::name($cat); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4">
                            <?php
                            if (!isset($post['error'])) {
                                $content = Posts::content($post[0]->content);
                                $title = Posts::title($post[0]->id);
                                $img = Gneex::getImage($content);
                                if ($img != '') {
                                    $im = '<img src="'.Url::thumb($img, 'large').'" class="img-responsive">';
                                } else {
                                    $im = '<img src="'.Url::thumb('assets/images/noimage.png', 'large').'" class="img-responsive">';
                                }
                                echo '
                                    <a href="'.Url::post($post[0]->id)."\">
                                    <div class=\"horizontal-list\">
                                        {$im}
                                    </div>
                                    <div class=\"\">
                                        <h4 >{$title}</h4>
                                    </div>
                                    </a>
                                ";
                                unset($post[0]);
                            } ?>
                            </div>
                            <div class="col-sm-8">
                            <ul class="list-unstyled">
                                <?php
                                foreach ($post as $p => $v) {
                                    echo '
                                    <li>
                                    <h5><a href="'.Url::post($v->id).'">'.$v->title.'</a></h5>
                                    </li>
                                    ';
                                } ?> 
                            </ul>
                            
                            </div>
                        </div>
                        
                    </div>
                </div>

                <?php
                if (Gneex::opt('adsense') != '') {
                    echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                } ?>


                <div class="row">
                    
                    <div class="col-sm-6">
                        <?php
                        $cat = $gneex['panel_2'];
                        $post = Posts::getPostCat($cat, 6); ?>
                        
                        <div class="panel panel-two">
                            <div class="panel-heading">
                            <h3 class="panel-title"><?=Categories::name($cat); ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                    <?php
                                    if (!isset($post['error'])) {
                                        $content = Posts::content($post[0]->content);
                                        $title = Posts::title($post[0]->id);
                                        $img = Gneex::getImage($content);
                                        if ($img != '') {
                                            $im = '<img src="'.Url::thumb($img, 'large').'" class="img-responsive">';
                                        } else {
                                            $im = '<img src="'.Url::thumb('assets/images/noimage.png', 'large').'" class="img-responsive">';
                                        }
                                        echo '
                                            <a href="'.Url::post($post[0]->id)."\">
                                            <div class=\"vertical-list\">
                                                {$im}
                                            </div>
                                            <div class=\"\">
                                                <h4 >{$title}</h4>
                                            </div>
                                            </a>
                                        ";
                                        unset($post[0]);
                                    } ?>
                                    </div>
                                    <div class="col-md-12">
                                    <ul class="list-unstyled">
                                        <?php
                                        foreach ($post as $p => $v) {
                                            echo '
                                            <li>
                                            <h5><a href="'.Url::post($v->id).'">'.$v->title.'</a></h5>
                                            </li>
                                            ';
                                        } ?> 
                                    </ul>
                                    
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                    
                    <div class="col-sm-6">
                        <?php
                        $cat = $gneex['panel_3'];
                        $post = Posts::getPostCat($cat, 6); ?>
                        
                        <div class="panel panel-three">
                            <div class="panel-heading">
                            <h3 class="panel-title"><?=Categories::name($cat); ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                    <?php
                                    if (!isset($post['error'])) {
                                        $content = Posts::content($post[0]->content);
                                        $title = Posts::title($post[0]->id);
                                        $img = Gneex::getImage($content);
                                        if ($img != '') {
                                            $im = '<img src="'.Url::thumb($img, 'large').'" class="img-responsive">';
                                        } else {
                                            $im = '<img src="'.Url::thumb('assets/images/noimage.png', 'large').'" class="img-responsive">';
                                        }
                                        echo '
                                            <a href="'.Url::post($post[0]->id)."\">
                                            <div class=\"vertical-list\">
                                                {$im}
                                            </div>
                                            <div class=\"\">
                                                <h4 >{$title}</h4>
                                            </div>
                                            </a>
                                        ";
                                        unset($post[0]);
                                    } ?>
                                    </div>
                                    <div class="col-md-12">
                                    <ul class="list-unstyled">
                                        <?php
                                        foreach ($post as $p => $v) {
                                            echo '
                                            <li>
                                            <h5><a href="'.Url::post($v->id).'">'.$v->title.'</a></h5>
                                            </li>
                                            ';
                                        } ?> 
                                    </ul>
                                    
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                </div>




                <?php
                if (Gneex::opt('adsense') != '') {
                    echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                } ?>
        

                <?php
                $cat = $gneex['panel_4'];
                    $post = Posts::getPostCat($cat, 4);
                    echo '<h4>'.Categories::name($cat).'</h4>'; ?>
                <hr />
                <div class="row">
                
                <ul class="list-featured">
                    <?php


                    foreach ($post as $p) {
                        $content = Posts::content($p->content);
                        $title = Posts::title($p->id);
                        $img = Gneex::getImage($content);
                        if ($img != '') {
                            $im = '<img src="'.Url::thumb($img).'" class="img-responsive">';
                        } else {
                            $im = '<img src="'.Url::thumb('assets/images/noimage.png').'" class="img-responsive">';
                        }

                        echo '<li class="col-sm-3">
                                    <a href="'.Url::post($p->id)."\">
                                    {$im}
                                    <div class=\"featured-text\">
                                        <h4 >{$title}</h4>
                                    </div>
                                    </a>
                                </li>";
                    } ?>
                    
                </ul>
                </div>


                


                <?php
                $cat = $gneex['panel_5'];
                    $post = Posts::getPostCat($cat, 8);
                    $postig = $post[0]; ?>
                
                <div class="panel panel-five">
                    <div class="panel-heading">
                    <h3 class="panel-title"><?=Categories::name($cat); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-8">
                            <ul class="list-unstyled">
                                <?php
                                unset($post[0]);
                                foreach ($post as $p => $v) {
                                    echo '
                                    <li>
                                    <h5><a href="'.Url::post($v->id).'">'.$v->title.'</a></h5>
                                    </li>
                                    ';
                                } ?> 
                            </ul>
                            
                            </div>
                            <div class="col-sm-4">
                            <?php
                            if (!isset($post['error'])) {
                                $content = Posts::content($postig->content);
                                $title = Posts::title($postig->id);
                                $img = Gneex::getImage($content);
                                if ($img != '') {
                                    $im = '<img src="'.Url::thumb($img, 'large').'" class="img-responsive">';
                                } else {
                                    $im = '<img src="'.Url::thumb('assets/images/noimage.png', 'large').'" class="img-responsive">';
                                }
                                echo '
                                    <a href="'.Url::post($postig->id)."\">
                                    <div class=\"horizontal-list\">
                                        {$im}
                                    </div>
                                    <div class=\"\">
                                        <h4 >{$title}</h4>
                                    </div>
                                    </a>
                                ";
                            } ?>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
                
                

                
                <?php
                } else {
                    ?>
                    
                    <!-- start blog layout -->

                    <div class=" blog-lists clearfix">
                    <?php
                    if ($data['num'] > 0) {
                        foreach ($data['posts'] as $p) {
                            echo '
                            <article class="blog-post col-md-12">
                                <h2><a href="'.Url::post($p->id)."\">$p->title</a></h2>
                                <hr />
                                ".Posts::format($p->content, $p->id).'
                                <div class="blog-footer">posted in '.Categories::name($p->cat).', at '.Date::format($p->date, 'd M Y H:i')." by <a href=\"#\">{$p->author}</a></div>
                            </article>
                                ";
                        }
                    } else {
                        echo 'No Post to Show';
                    } ?>
                    
                    </div>
                    <?=$data['paging']; ?>


                    <!-- end blog layout -->
                    <?php
                }
            ?>
            </div> <!-- col-md-8 blog-lists end -->
            <?php Theme::theme('rightside', $data); ?>
                

        </div>
    </section>