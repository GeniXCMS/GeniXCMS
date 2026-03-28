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
        <p></p>
        <?php 
        $post_image = Posts::getPostImage($post->id);
        if( $post_image != "" ) {
            $imgurl = Url::thumb($post_image, 'large', 850);
        ?>
        <p><img src="<?=$imgurl;?>" class="img-fluid post_image"></p>
        <?php } ?>
        <?=Hooks::run('page_content_before_action', $data);?>
        <?=Posts::content($post->content);?>
        <?=Hooks::run('page_content_after_action', $data);?>
        <p class="blog-post-meta fs-6 fst-italic text-body-emphasis">Last Modified on <?=Date::format($post->modified);?></p>
    </article>


</div>

<?php
include "sidebar.php";
echo "</div>";
