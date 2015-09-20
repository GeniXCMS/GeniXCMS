    

    <hr>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <ul class="list-inline text-center">
                      <?php
                        if(null != Options::v('twitter')){
                      ?>
                        <li>
                            <a href="https://twitter.com/<?=Options::v('twitter');?>">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                      <?php } 
                        if(null != Options::v('fbacc')){
                      ?>
                        <li>
                            <a href="https://facebook.com/<?=Options::v('fbacc');?>">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                      <?php } 
                        if(null != Options::v('linkedin')){
                      ?>
                        <li>
                            <a href="#">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                      <?php }  ?>
                    </ul>
                    <p class="copyright text-muted">Copyright &copy; <?=Site::$name;?> <?=date("Y");?></p>
                    <p class="copyright text-muted">Powered by <a href="http://genixcms.org" title="Free and Opensource CMS">GeniXCMS <?=System::v();?></p>
                </div>
            </div>
        </div>
    </footer>


    <?=Site::footer();?>
    <link href="<?=Site::$url;?>/assets/css/genixfont.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=Site::$url;?>/inc/themes/cleanblog/css/clean-blog.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

  </body>
</html>
