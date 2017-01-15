
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-12 text-center footer-copyright">
                    <small>Copyright &copy; <?=date('Y');
?>, <?=Site::$name;?>. All Rights reserved. 
                    powered by <a href="http://genixcms.org">GeniXCMS</a></small>
                </div>
            </div>
        </div>
        
    </footer>
    <span href="#" class="scrollup"><i class="fa fa-arrow-up fa-2x"></i></span>
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
        });

        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').click(function () {
            $("html, body").animate({
                scrollTop: 0
            }, 600);
            return false;
        });
    </script>
  </body>
</html>