    <section id="innerslide">
        
        <div class="bg-slide">
            
        </div>

    </section>
    <section id="blog">
        <div class="container">
            
                <div class="col-md-8">
                    <div class=" blog-lists clearfix">
                    <?php
                    if (Gneex::opt('adsense') != '') {
                        echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                    }
                    if (isset($data['posts'][0]->title)) {
                        foreach ($data['posts'] as $p) {
                            $comment = array(
                            'offset' => 0,
                            'max' => Options::v('comments_perpage'),
                            'parent' => 0,
                            );
                            echo '
                                <article class="blog-post col-md-12">
                                    <h2><a href="'.Url::post($p->id)."\">$p->title</a></h2>
                                    
                                    <span class=\"meta\">posted in <a href=\"".Url::cat($p->cat).'">'.Categories::name($p->cat).'</a>, at '.Date::format($p->date).' by '.$p->author.'</span>
                                    <hr />
                                    '.Posts::content($p->content, $p->id).'<hr />';
                            if (Gneex::opt('adsense') != '') {
                                echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                            }
                            echo '
                                    
                                    <h3>Related :</h3>
                                    '.Posts::related($p->id, 4, $p->cat, 'box').'
                                </article>';
                            if (Comments::isEnable()) {
                                echo '
                                <div class="col-sm-12">
                                    <h3>Comments</h3>
                                    <div class="row">'.Comments::form().'<div class="clearfix">&nbsp;</div><hr />'.Comments::showList($comment).'</div>
                                </div>
                                    ';
                            }
                        }
                    } else {
                        Control::error('404');
                    }
                    ?>
                        
                    </div>
                </div>
                <?php Theme::theme('rightside', $data); ?>
                

        </div>
    </section>
