<div class="col-sm-8 blog-main">
<?php
if (mdoTheme::opt('mdo_adsense') != '') {
    echo '<div class="row"><div class="col-md-12">'.mdoTheme::opt('mdo_adsense').'</div></div><hr />';
}
if (isset($data['posts'][0]->title)) {
    foreach ($data['posts'] as $p) {
        echo '
        <div class="blog-post">
            <h2 class="blog-post-title"><a href="'.Url::post($p->id).'">'.$p->title.'</a></h2>
            <p class="blog-post-meta">'.Date::format($p->date).' '._('by').' <a href="'.Url::author($p->author).'">'.$p->author.'</a></p>';

            
        $post_image = Posts::getPostImage($p->id);
        if( $post_image != "" ) {
            $imgurl = Url::thumb($post_image, 'large', 845);
        
        echo '<p><img src="'.$imgurl.'" width="845" class="img-fluid post_image mx-auto" alt="'.$post->title.'" loading="lazy"></p>';
        }
        echo Posts::content($p->content).'
            <span>'.Posts::tags($p->id).'</span>
            <hr />';
        if (mdoTheme::opt('mdo_adsense') != '') {
            echo '<div class="row">
                <div class="col-md-6">'.mdoTheme::opt('mdo_adsense').'</div>
                <div class="col-md-6">'.mdoTheme::opt('mdo_adsense').'</div>
            </div><hr />';
        }
        $comment = array(
                'offset' => 0,
                'max' => 5,
                'parent' => 0,
            );
        echo '
            <h3>'._('Related :').'</h3>
            <div class="row">
            '.Posts::related($p->id, 5, $p->cat, 'box', 50).'
            </div>
        </div>
        <hr />
        <div class="col-sm-12">
            <div class="row">
            <h3>'._('Comments').'</h3>
            '.Comments::form().'<div class="clearfix">&nbsp;</div><hr />'.Comments::showList($comment).'
            <!--<div class="fb-comments" data-href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" data-width="100%" data-numposts="5" data-colorscheme="light"></div>-->
            </div>
        </div>
            ';
    }
} else {
    //echo "Error, Post not found.";
    if (mdoTheme::opt('mdo_adsense') != '') {
        echo mdoTheme::opt('mdo_adsense').'<hr />';
    }
    Control::error('404');
}

?>
</div>
<?php Theme::theme('rightside', $data); ?>
