    <section id="innerslide">
        
        <div class="bg-slide">
            
        </div>

    </section>
    <section id="blog">
        <div class="container">
            
                <div class="col-md-8">
                    <div class=" blog-lists clearfix">
                        <article class="blog-post">
                            <?php
                            if (Gneex::opt('adsense') != '') {
                                echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                            }
                            ?>
                            <?php
                            echo "<div class=\"blog-main col-md-12\">
                            <h2>".Mod::getTitle($data['mod'])."</h2><hr/>";
                            Hooks::run('mod_control', $data);
                            echo "</div>";
                            ?>
                            <?php
                            if (Gneex::opt('adsense') != '') {
                                echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div>';
                            }
                            ?>
                        </article>
                        
                    </div>
                </div>
                <?php Theme::theme('rightside', $data); ?>
                

        </div>
    </section>
