<?php
$bsmag = Options::v('bsmag_theme_options');
$opt = json_decode($bsmag, true);
$about = $opt['bsmag_about_site'];
?>
    <div class="col-md-4">
      <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-body-tertiary rounded">
          <h4 class="fst-italic">About</h4>
          <p class="mb-0"><?=$about;?></p>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">Recent posts</h4>
          <ul class="list-unstyled">
            <?php 
            $rec = Posts::recent(['num' => 5]);
            // print_r($rec);

            foreach( $rec as $k => $v ) {
              $post_image = Posts::getPostImage($v->id);
              $img = ( $post_image != "" ) ? $post_image: Posts::getImage(Typo::Xclean($v->content), 1);
              $imgurl = $img == "" ? Url::thumb(Site::$url."assets/images/noimage.png", 'square', 100): Url::thumb($img, 'square', 100);
            ?>
            <li>
              <a title="<?=$v->title;?>" class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="<?=Url::post($v->id);?>">
                <img src="<?=$imgurl;?>" width="100" height="100" class="bd-placeholder-img" aria-hidden="true" alt="<?=$v->title;?>" loading="lazy">
                <div class="col-lg-8">
                  <h6 class="mb-0"><?=$v->title;?></h6>
                  <small class="text-body-secondary"><?=Date::format($v->date);?></small>
                </div>
              </a>
            </li>
            <?php
            }
            ?>
            
          </ul>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">Archives</h4>
          <?php
            echo Archives::list(10);
          ?>
        </div>


        <div class="p-4">
          <h4 class="fst-italic">Elsewhere</h4>
          <ol class="list-unstyled">
          </ol>
        </div>
      </div>
    </div>