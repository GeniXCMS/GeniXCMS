        <div class="col-md-4 ">
        <?php
        if (Gneex::opt('adsense') != '') {
            echo '<div class="row"><div class="col-md-12">'.Gneex::opt('adsense').'</div></div><hr />';
        }
        ?>
        <div class="row">
          <div class="col-sm-6 col-md-12">
            <div class="panel panel-green">
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
          </div>

          <div class="col-sm-6 col-md-12">
            <div class="panel panel-black ">
              <div class="panel-heading">
                <h3 class="panel-title">Recent Comments</h3>
              </div>
              <div class="panel-body">
              <?php
                echo Comments::recent();
              ?>
              </div>
              
            </div>
          </div>


          <div class="col-sm-6 col-md-12">
            <div class="panel panel-red ">
              <div class="panel-heading">
                <h3 class="panel-title">Tags</h3>
              </div>
              <div class="panel-body">
              <?php
                echo Tags::cloud();
              ?>
              </div>
              
            </div>
          </div>


          <div class="col-sm-6 col-md-12">
            <div class="panel panel-blue ">
              <div class="panel-heading">
                <h3 class="panel-title">Related Site</h3>
              </div>
              <div class="panel-body">
                <ol class="list-unstyled">
                  <li><a href="http://genix.me">GeniXCMS</a></li>
                  <li><a href="http://docs.genix.me">GeniXCMS Docs</a></li>
                </ol>
              </div>
              
            </div>
          </div>
          
        </div>
          
        </div><!-- /.blog-sidebar -->
