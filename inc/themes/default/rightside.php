        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
          <div class="sidebar-module sidebar-module-inset">
            <h4>About</h4>
            <h5><?=Site::logo('','40px');?> <?=Site::$name;?></h5>
            <p><em><?=Options::get('siteslogan');?></em>
            <?=Site::$desc;?></p>
          </div>
          <div class="sidebar-module">
            <h4>Recent Post</h4>
            <ol class="list-unstyled">
            <?php
                $recent = Posts::recent(10);
                $num = Db::$num_rows;
                if($num > 0) {
                  foreach ($recent as $r) {
                      # code...
                      echo "<li><a href=\"".Url::post($r->id)."\">$r->title</a></li>
                          ";
                  }
                }else{
                  echo "No Post to Show";
                }
            ?>
            </ol>
          </div>
          <div class="sidebar-module">
            <h4>Elsewhere</h4>
            <ol class="list-unstyled">
              <li><a href="#">GitHub</a></li>
              <li><a href="#">Twitter</a></li>
              <li><a href="#">Facebook</a></li>
            </ol>
          </div>
        </div><!-- /.blog-sidebar -->
