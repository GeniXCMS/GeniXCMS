<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
          <div class="sidebar-module sidebar-module-inset">
            <h4>About</h4>
            <h5><span class="mg genixcms-logo"></span>GeniXCMS</h5>
            <p><em>pre build version (0.0.1pre)</em>
            developed for easyness customization and module additions</p>
          </div>
          <div class="sidebar-module">
            <h4>Recent Post</h4>
            <ol class="list-unstyled">
            <?php
                $recent = Db::result("SELECT * FROM `posts` WHERE `type` = 'post' ORDER BY `date` DESC LIMIT 10");
                foreach ($recent as $r) {
                    # code...
                    echo "<li><a href=\"".Url::post($r->id)."\">$r->title</a></li>
                        ";
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