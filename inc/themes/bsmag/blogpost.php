
    <div class="col-md-8">
    <h3 class="pb-4 mb-4 fst-italic border-bottom">
      <?php
      // print_r($data);
      if( isset($data['p_type']) ) {
        if( $data['p_type'] == 'index' ) {
          echo "From the ".Site::$name;
        }
        if( $data['p_type'] == 'archive' ) {
          echo "Archive From ".$data['dateName'];
        }
        if( $data['p_type'] == 'cat' ) {
          echo "Posts of ".Categories::name($data['cat']);
        }
        if( $data['p_type'] == 'author' ) {
          echo "Posts by ".$data['author'];
        }

        if( $data['p_type'] == 'tag' ) {
          echo "#".$data['tag'];
        }

        if( $data['p_type'] == 'search' ) {
          echo "Keyword: ".$data['q'];
        }
        
        
      } else {
        echo "From the ".Site::$name;
      }

      
      ?>
      </h3>

      <?php
      // print_r($data);
      if( !isset($data['posts']['error']) ) {
        foreach( $data['posts'] as $k => $v ) {
          $style = ['success', 'primary', 'danger', 'warning'];
          $rn = rand(0, 3);
      ?>
      <article class="blog-post">
        <h2 class="display-5 link-body-emphasis mb-1"><a href="<?=Url::post($v->id);?>" class="link-body-emphasis" title="<?=$v->title;?>"><?=$v->title;?></a></h2>
        <p class="blog-post-meta">Posted Inside <a href="<?=Url::cat($v->cat);?>" class="badge text-bg-<?=$style[$rn];?>"><?=Categories::name($v->cat);?></a> <?=Date::format($v->date);?> by <a href="<?=Url::author($v->author);?>"><?=$v->author;?></a></p>

        <?=Posts::format(Typo::Xclean($v->content), $v->id);?>
      </article>
      <?php
        }
      }

      echo $data['paging'];
      ?>
      


    </div>
