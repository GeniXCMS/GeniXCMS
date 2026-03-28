<?php
$bsmag = Options::v('bsmag_theme_options');
$opt = json_decode($bsmag, true);

$feat = $opt['bsmag_featured_posts'];
$feats = explode(',', $feat);



// print_r($content);

?>

<div class="row mb-2">
  <?php
  foreach( $feats as $k => $v ) {
    $title_post = Posts::title($v);
    $title = substr($title_post, 0, 20);
    $titleEnd = strlen($title) > 19 ? "...":"";
    $post_content = Posts::getPostContent($v);
    $content = strip_tags($post_content);
    $content = substr($content, 0, 100);
    $catname = Categories::name( Posts::cat($v) );
    $style = ['success', 'primary', 'danger', 'warning'];
    $rn = rand(0, 3);
    $date = Date::format(Posts::date($v));
    $post_image = Posts::getPostImage($v);
    $img = ( $post_image != "" ) ? $post_image: Posts::getImage($post_content);
    $imgurl = $img == "" ? Url::thumb(Site::$cdn."assets/images/noimage.png", 'square', 200): Url::thumb($img, 'square', 250);
    ?>
    

    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <strong class="d-inline-block mb-2 text-<?=$style[$rn];?>"><?=$catname;?></strong>
          <h3 class="mb-0" title="<?=$title_post;?>"  alt="<?=$title_post;?>"><?=$title.$titleEnd;?></h3>
          <div class="mb-1 text-body-secondary"><?=$date;?></div>
          <p class="card-text mb-auto"><?=$content;?></p>
          <a href="<?=Url::post($v);?>"  title="<?=$title_post;?>" class="icon-link gap-1 icon-link-hover stretched-link">
            Continue reading
            <svg class="bi"><use xlink:href="#chevron-right"/></svg>
          </a>
        </div>
        <div class="col-auto d-none d-lg-block" >
          <div style="width: 180px; height: 250px; background-image: url('<?=$imgurl;?>'); background-size: cover; background-position: center center">
            &nbsp;
          <!-- <img src="<?=$img;?>" class="img-fluid"> -->
          </div>
          
        </div>
      </div>
    </div>

  <?php
  }
  ?>
    
    
  </div>