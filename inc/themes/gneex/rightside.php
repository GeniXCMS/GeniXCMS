        <div class="col-md-4 ">
        <?php
        if (Gneex::opt('adsense') != '') {
            echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
        }
        ?>
          <div class="panel panel-red">
            <div class="panel-heading">
              <h3 class="panel-title">Recent Post</h3>
            </div>
            <div class="panel-body">

<?php
        $vars = array(
        'num' => 5,
        'title' => true,
        // 'excerpt' => true,
        // 'excerpt_max' => 50,
        'image' => true,
        'class' => array(
            'img' => 'listNews',
          ),
        );
        Posts::lists($vars);
?>
            </div>
          </div>
          <div class="panel panel-red ">
            <div class="panel-heading">
              <h3 class="panel-title">Related Site</h3>
            </div>
            <div class="panel-body">
              <ol class="list-unstyled">
                <li><a href="https://metalgenix.com">MetalGeniX</a></li>
                <li><a href="https://genixcms.org">GeniXCMS</a></li>
                <li><a href="https://docs.genixcms.org">GeniXCMS Docs</a></li>
              </ol>
            </div>
            
          </div>
        </div><!-- /.blog-sidebar -->
