<?php

echo "<div class=\"row g-5 mb-5\">";

?>

<div class="col-md-8">
    <article class="blog-post">
<?php
if( !isset($data['posts']['error'])) {
    $post = $data['posts'][0];
}

?>
        <h2 class="display-5 link-body-emphasis mb-1"><?=$post->title;?></h2>
        <p class="blog-post-meta text-primary-emphasis">Posted inside <a href="<?=Url::cat($post->cat);?>" class="badge text-bg-primary"><?=Categories::name($post->cat);?></a> on <?=Date::format($post->date);?> by <a href="<?=Url::author($post->author);?>"><?=$post->author;?></a></p>
        <?php 
        $post_image = Posts::getPostImage($post->id);
        if( $post_image != "" ) {
            $imgurl = Url::thumb($post_image, 'large', 850);
        ?>
        <p><img src="<?=$imgurl;?>" width="850" class="img-fluid post_image" alt="<?=$post->title;?>" loading="lazy"></p>
        <?php } ?>
        <?=Hooks::run('post_content_before_action', $data);?>
        <?=Posts::content($post->content);?>
        <?=Hooks::run('post_content_after_action', $data);?>
    </article>
    <span><?=Posts::tags($post->id);?></span>
    <hr>
    <div class="col-12">
    <h3><?=_('Related Post');?></h3>
    <?php
    $rel = Posts::related($post->id, 8, $post->cat);
    print_r($rel);
    ?>
    </div>
</div>

<?php
include "sidebar.php";
echo "</div>";
