

            <br />
        </div>

    </div>
    <footer class="footer">
        <small>Copyright &copy; <?=date("Y");?> <a href="http://genixcms.org">GeniXCMS</a> <i><?=System::v();?></i></small><br />
        <?php
            $end_time = microtime(TRUE);
            $time_taken = $end_time - $GLOBALS['start_time'];
            $time_taken = round($time_taken,5);
            echo '<small>Page generated in '.$time_taken.' seconds.</small>';
        ?>
    </footer>
    
    <?php
      Theme::editor();
      Site::footer();

    ?>
    <link href="<?=Site::$url;?>/assets/css/bootstrap-theme.min.css" rel="stylesheet">
    <script>
         $("#selectall").change(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
              //alert(cb.val());
           
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip({
                    placement: 'top'
                });
        });
    </script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=Site::$url;?>/assets/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?=Site::$url;?>/assets/js/sb-admin-2.js"></script>

    <!-- MetisMenu CSS -->
    <link href="<?=Site::$url;?>/assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?=Site::$url;?>/assets/css/sb-admin-2.css" rel="stylesheet">

    <link href="<?=Site::$url;?>/assets/css/genixfont.css" rel="stylesheet">
  </body>
</html>

