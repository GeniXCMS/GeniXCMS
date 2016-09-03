        <section id="innerslide">
        
        <div class="bg-slide">
            
        </div>

    </section>
    <section id="blog">
        <div class="container">
            
                <div class="col-md-8">
                    <div class="blog-lists clearfix">
                    <?php
                    if (Gneex::opt('adsense') != '') {
                        echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                    }
                    ?>
                    <?php

                    if (isset($data['posts'][0]->title)) {
                        foreach ($data['posts'] as $p) {
                            echo "
                                <article class=\"blog-post col-md-12\">
                                    <h2>$p->title</h2>
                                    <hr />
                                    ".Posts::content($p->content, $p->id).'
                                </article>
                                    ';
                        }
                    } else {
                        Control::error('404');
                    }

                    ?>
                    <?php
                    if (Gneex::opt('adsense') != '') {
                        echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                    }
                    ?>
                    </div>
                </div>
                <?php Theme::theme('rightside', $data); ?>
                

        </div>
    </section>
