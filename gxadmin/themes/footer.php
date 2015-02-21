

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
    <script>
         $("#selectall").change(function(){
              $('input:checkbox').not(this).prop('checked', this.checked);
              //alert(cb.val());
        });
    </script>
<!-- <link href="<?=Options::get('siteurl');?>/assets/css/dashboard.css" rel="stylesheet"> -->
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=Options::get('siteurl');?>/assets/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="<?=Options::get('siteurl');?>/assets/js/plugins/morris/raphael.min.js"></script>
    <script src="<?=Options::get('siteurl');?>/assets/js/plugins/morris/morris.min.js"></script>
    <script src="<?=Options::get('siteurl');?>/assets/js/plugins/morris/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?=Options::get('siteurl');?>/assets/js/sb-admin-2.js"></script>

    <!-- MetisMenu CSS -->
    <link href="<?=Options::get('siteurl');?>/assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="<?=Options::get('siteurl');?>/assets/css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?=Options::get('siteurl');?>/assets/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?=Options::get('siteurl');?>/assets/css/plugins/morris.css" rel="stylesheet">


    <link href="<?=Options::get('siteurl');?>/assets/css/genixfont.css" rel="stylesheet">
  </body>
</html>

