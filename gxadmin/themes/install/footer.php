

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
    
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <link href="assets/css/install.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/genixfont.css" rel="stylesheet">
  </body>
</html>

