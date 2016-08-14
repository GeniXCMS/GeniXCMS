<div class="col-sm-8 blog-main">
<?php
    echo "<div class=\"row\"><div class=\"col-md-12\">".MdoTheme::opt('mdo_adsense')."</div></div><hr />";
    if ($data['num'] > 0) {
        foreach ($data['posts'] as $p) {
            echo "
            <div class=\"blog-post\">
                <h2 class=\"blog-post-title\"><a href=\"".Url::post($p->post_id)."\">$p->title</a></h2>
                <p class=\"blog-post-meta\">".Date::format($p->date)." by <a href=\"#\">{$p->author}</a></p>
                ".Posts::format($p->content, $p->post_id)."
            </div>
                ";
        }

    } else {
        echo "No Post to show";
    }
    echo $data['paging'];
?>
</div>
<?php Theme::theme('rightside', $data); ?>
