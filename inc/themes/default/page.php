<div class="col-sm-8 blog-main">
<?php
    if (mdoTheme::opt('mdo_adsense') != '') {
        echo "<div class=\"row\"><div class=\"col-md-12\">".mdoTheme::opt('mdo_adsense')."</div></div><hr />";
    }
    foreach ($data['posts'] as $p) {
        
        echo "
        <div class=\"blog-post\">
            <h2 class=\"blog-post-title\">$p->title</h2>
           
           ".Typo::Xclean($p->content)."
        </div>
            ";
    }
?>
</div>
<?php Theme::theme('rightside', $data); ?>