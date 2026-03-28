<?php
$bsmag = Options::v('bsmag_theme_options');
$opt = json_decode($bsmag, true);

$intro_img = Typo::Xclean($opt['bsmag_intro_img']);
$intro_post = $opt['bsmag_intro_post_id'];
$title = Posts::title($intro_post);
$content = Posts::getPostContent($intro_post);
$content = strip_tags($content);
$content = substr($content, 0, 200);

$style = $intro_img != "" ? "background-image: url('{$intro_img}'); background-size: cover; background-position: center center; background-repeat: no-repeat; color: #fefefe !important": "";
$linkStyle = $intro_img != "" ? "color: #fefefe !important": "";
// print_r($content);

?>

<div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="<?=$style;?>">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic"><?=$title;?></h1>
      <p class="lead my-3"><?=$content;?></p>
      <p class="lead mb-0"><a href="<?=Url::post($intro_post);?>" class="text-body-emphasis fw-bold" style="<?=$linkStyle;?>"><?=_('Continue reading...');?></a></p>
    </div>
  </div>