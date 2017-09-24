
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h4>Navigation</h4>
                    <?php
                        echo Menus::getMenu('footer', 'list-unstyled', true);
                    ?>
                </div>
                <div class="col-md-5">
                    <h4>Tags</h4>
                    <?php
                        echo Tags::cloud();
                    ?>
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-12 text-center footer-copyright">
                    <small>Copyright &copy; <?=date('Y');
?>, <?=Site::$name;?>. All Rights reserved. 
                    powered by <a href="http://genix.id">GeniXCMS</a></small>
                </div>
            </div>
        </div>
        
    </footer>

    <!-- LIBRARY -->
    
    
    <?=Site::footer();?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.6.2/jquery.flexslider.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('.featured').flexslider({
                animation: "slide",
                animationLoop: false,
                itemWidth: 260,
                itemMargin: 30
              });
        })
    </script>
  </body>
</html>