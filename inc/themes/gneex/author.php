<?php
$gneex = Gneex::$opt;
?>
    <section id="innerslide">
        
        <div class="bg-slide">
            
        </div>

    </section>

    <section id="blog">
        <div class="container">
            
            <div class="col-md-8">
                <h2 class="category-title"><?=$data['author'];?></h2>
                <hr />
                <div class=" blog-lists clearfix">
                <?php
                if (Gneex::opt('adsense') != '') {
                    echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                } else {
                    echo '<div class="col-md-12">&nbsp;</div>';
                }
                ?>
                <?php
                if ($data['num'] > 0) {
                    foreach ($data['posts'] as $p) {
                        if ($gneex['category_layout'] == 'magazine') {
                            # code...
                            $img = Posts::getImage(Posts::content($p->content));
                            if ($img != '') {
                                $im = '<img src="'.Url::thumb($img, '', 300).'" class="img-responsive">';
                            } else {
                                $im = '<img src="'.Url::thumb('assets/images/noimage.png', '', 300).'" class="img-responsive">';
                            }
                            echo '
                            <article class="blog-post magazine col-md-12 clearfix">
                            <div class="col-sm-4 col-md-4">
                            '.$im.'
                            </div>
                            <div class="col-sm-8 col-md-8">
                                <h3><a href="'.Url::post($p->id).'">'.$p->title.'</a></h3>
                                <div class="blog-meta"><small>published at '.Date::format($p->date, 'd M Y');
                            if ($p->type != 'page') {
                                echo " in <a href=\"" . Url::cat($p->cat) . "\">" . Categories::name($p->cat) . "</a>";
                            }
                            echo "
                                by <a href=\"".Url::author($p->author)."\">{$p->author}</a></small><br /><br /></div>
                                ".substr(Typo::strip(Posts::content($p->content)), 0, 350).'
                            </div>
                            <div class="col-sm-12 col-md-12 clearfix">
                                <hr />
                            </div>
                            </article>
                                ';
                        } else {
                            echo '
                            <article class="blog-post col-md-12">
                                <h2><a href="'.Url::post($p->id)."\">$p->title</a></h2>
                                <hr />
                                ".Posts::format($p->content, $p->id).'
                                <div class="blog-footer">posted in '.Categories::name($p->cat).', 
                                at '.Date::format($p->date)." by <a href=\"".Url::author($p->author)."\">{$p->author}</a></div>
                            </article>
                                ";
                        }
                    }
                } else {
                    echo 'No Post to Show';
                }
                ?>
                <?php
                if (Gneex::opt('adsense') != '') {
                    echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                }
                ?>
                </div>
                <?=$data['paging'];?>
            </div>
            <?php Theme::theme('rightside', $data); ?>
                

        </div>
    </section>
