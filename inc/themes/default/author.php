<div class="col-sm-8 blog-main">
    <h2>Post By : <?=$data['author'];?></h2>
    <hr>
<?php
if (mdoTheme::opt('mdo_adsense') != '') {
    echo '<div class="row"><div class="col-md-12">'.mdoTheme::opt('mdo_adsense').'</div></div><hr />';
}
if ($data['num'] > 0) {
    foreach ($data['posts'] as $p) {
        echo '
        <div class="blog-post">
            <h2 class="blog-post-title"><a href="'.Url::post($p->id)."\">$p->title</a></h2>
            <p class=\"blog-post-meta\">".Date::format($p->date)."</p>
            ".Posts::format($p->content, $p->id).'
        </div>
            ';
    }
} else {
    echo _('No Post to show');
}
echo $data['paging'];
?>
</div>
<?php Theme::theme('rightside', $data); ?>