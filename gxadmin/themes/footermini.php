




<?php

echo Hooks::run('admin_footer_action', $data);
?>


<!-- Morris.js charts -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
<!--<script src="--><?//=Site::$url;?><!--assets/plugins/morris/morris.min.js"></script>-->
<!-- Sparkline -->
<script src="<?=Site::$url;?>assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?=Site::$url;?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?=Site::$url;?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?=Site::$url;?>assets/plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
<!--<script src="--><?//=Site::$url;?><!--assets/plugins/daterangepicker/daterangepicker.js"></script>-->
<!-- datepicker -->
<!--<script src="--><?//=Site::$url;?><!--assets/plugins/datepicker/bootstrap-datepicker.js"></script>-->
<!-- Bootstrap WYSIHTML5 -->
<script src="<?=Site::$url;?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?=Site::$url;?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?=Site::$url;?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?=Site::$url;?>assets/js/app.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=Site::$url;?>assets/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?=Site::$url;?>assets/js/demo.js"></script>
<!-- Bootstrap slider -->
<script src="<?=Site::$url;?>assets/plugins/bootstrap-slider/bootstrap-slider.js"></script>
<!-- iCheck -->
<script src="<?=Site::$url;?>assets/plugins/iCheck/icheck.min.js"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button);
    $('.slider').bootstrapSlider();

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

    setTimeout(function () {
        $("#notification").fadeOut();
    }, 5000);
</script>


</body>
</html>