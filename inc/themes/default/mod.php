    <section class="col-sm-8 blog-main">
        <?php
        if (mdoTheme::opt('mdo_adsense') != '') {
            echo '<div class="row"><div class="col-md-12">'.mdoTheme::opt('mdo_adsense').'</div></div><hr />';
        }
        ?>
        <div class="blog-post">
            <h2 class="blog-post-title"><?=Mod::getTitle($data['mod']);?></h2>
        <?php
        Hooks::run('mod_control', $data);
        ?>

        </div>
    </section>
<?php Theme::theme('rightside', $data); ?>