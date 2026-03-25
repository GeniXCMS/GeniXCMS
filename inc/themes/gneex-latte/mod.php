    <section id="blog">
        <div class="container">
            
                <div class="col-md-8">
                    <div class=" blog-lists clearfix">
                        <article class="blog-post col-md-12">
                            <?php
                            if (Gneex::opt('adsense') != '') {
                                echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
                            }
                            ?>
                            <?php
                            Hooks::run('mod_control', $data);
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
