    </div><!-- /.row -->

    </div><!-- /.container -->
    <div class="blog-footer">
      <p>Blog template built for <a href="http://getbootstrap.com">Bootstrap</a> by <a href="https://twitter.com/mdo">@mdo</a>.</p>
      <p>Powered by <a href="https://genixcms.web.id/" title="Free and Opensource CMS">GeniXCMS <?=System::v();?></p>
      <p>
        <a href="#">Back to top</a>
      </p>

    </div>
    

    <?php
      echo Site::footer();
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <link href="<?=Site::$url;?>/inc/themes/default/css/blog.css" rel="stylesheet">
    <style>
        #code {
            bottom: 0;
            top: 0;
        }
    </style>
    <?=mdoTheme::opt('mdo_analytics');?>
  </body>
</html>
