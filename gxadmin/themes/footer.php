</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> <?=System::$version;?>
    </div>
    <strong>Copyright &copy; 2014-<?=date('Y');?> <a href="https://genix.me" target="_blank">GeniXCMS</a>.</strong> All rights
    reserved.
    <?php
    $end_time = microtime(true);
    $time_taken = $end_time - $GLOBALS['start_time'];
    $time_taken = round($time_taken, 5);
    echo '<small>Page generated in '.$time_taken.' seconds.</small> <br />';
    ?>
</footer>


</div>
<!-- ./wrapper -->

<span href="#" class="scrollup"><i class="fa fa-arrow-up fa-2x"></i></span>



<?php
if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
    Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));

    $url = Url::ajax('saveimage');
    $foot = '
    <script>
      $(document).ready(function() {

        function sendFile(file,editor,welEditable) {
          data = new FormData();
          data.append("file", file);
            $.ajax({
                url: \''.$url.'\',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: \'POST\',
                success: function(data) {
                //alert(data);
                  $(\'.editor\').summernote(\'editor.insertImage\', data);
                },
               error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus+\' \'+errorThrown);
               }
            });
          }

        $(\'.editor\').each(function(i, obj) { $(obj).summernote({
            minHeight: 300,
            maxHeight: ($(window).height() - 150),
            toolbar: [
                    '.System::$toolbar.'
                ],
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0],editor,welEditable);
                },
                onPaste: function (e) {
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData(\'Text\');
                    e.preventDefault();
                    document.execCommand(\'insertText\', false, bufferText);
                },
                onChange: function(e) {
                    var characteres = $(".note-editable").text();
                    var wordCount = characteres.trim().split(\' \').length;
                    if (characteres.length == 0) {
                        $(\'.note-statusbar\').html(\'&nbsp; 0 word <div class="note-resizebar">    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>  </div>\');
                        return;
                    }
                    //Update value
                    $(".note-statusbar").html(\'&nbsp; \'+wordCount+\' words <div class="note-resizebar">    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>  </div>\');
     
                }
            },
            popover: {
            image: [
                [\'imagesize\', [\'imageSize100\', \'imageSize50\', \'imageSize25\']],
                [\'floatBS\', [\'floatBSLeft\', \'floatBSNone\', \'floatBSRight\']],
                [\'custom\', [\'imageAttributes\',\'imageShape\']],
                [\'remove\', [\'removeMedia\']]
            ],
            dialogsInBody: true,
        },
          });
        });


        
    });

    </script>
              ';
    echo Site::minifyJS($foot);
}

?>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'top'
        });

        $('#dateFrom').datetimepicker({
            format: 'YYYY/MM/DD'
        });
        $('#dateTo').datetimepicker({
            format: 'YYYY/MM/DD',
            useCurrent: false //Important! See issue #1075
        });
        $("#dateFrom").on("dp.change", function () {
            $('#dateTo').data("DateTimePicker").minDate(e.date);
        });
        $("#dateTo").on("dp.change", function () {
            $('#dateFrom').data("DateTimePicker").maxDate(e.date);
        });
        $('#dateTime').datetimepicker({
            format: 'YYYY/MM/DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
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
    });

    <?php
    $tagAjax = Url::ajax('tags');
    $versionUrl = Url::ajax('version');
    ?>
    $('#tags').tagsInput({
        width: 'auto',
        autocomplete_url: '<?=$tagAjax;?>',
        autocomplete:{selectFirst:true,width:'100px',autoFill:true}
    });

    setTimeout(
        function() {
            $.getJSON('<?=$versionUrl;?>',function(a) {
                // console.log(a.status);

            }).done(function(obj,status,xhdr) {
                // console.log(obj);
                if (obj.status == 'false') {
                    // console.log('false');

                    $('#notification').html('<div id="version" class="label label-danger" style="position: absolute; margin-left: auto; margin-right: auto;  top: 35px; white-space: wrap; width: auto"><span class="fa fa-warning"></span> Warning: Your CMS version is outdated. <span class="hidden-xs hidden-sm">New version is ready to upgrade (<strong>'+obj.version+'</strong>).</span></div>');
                    // $('#version').fadeIn();
                    $('#version').animate({top: 50}, 1000);
                    setTimeout(
                        function() {
                            $('#version').fadeOut();
                        },10000
                    );
                }
            }).error(function() {

            })
        },5000
    );

    setTimeout(function () {
        $("#notification").fadeOut();
    }, 5000);

</script>


<!-- Morris.js charts -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
<!-- <script src="<?=Site::$url;?>assets/plugins/morris/morris.min.js"></script> -->
<!-- Sparkline -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
<!-- jQuery Knob Chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
<!-- datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?=Site::$url;?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js"></script>
<!-- iCheck -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=Site::$url;?>assets/js/app.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=Site::$url;?>assets/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?=Site::$url;?>assets/js/demo.js"></script>
<!-- Bootstrap slider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>


<script>
    $.widget.bridge('uibutton', $.ui.button);
    $('.slider').bootstrapSlider();
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
//        increaseArea: '20%' // optional
    });

//    $(function () {
        var checkAll = $('input#selectall');
        var checkboxes = $('input#select');

//        $('input').iCheck();

        checkAll.on('ifChecked ifUnchecked', function(event) {
            if (event.type == 'ifChecked') {
                checkboxes.iCheck('check');
            } else {
                checkboxes.iCheck('uncheck');
            }
        });

        checkboxes.on('ifChanged', function(event){
            if(checkboxes.filter(':checked').length == checkboxes.length) {
                checkAll.prop('checked', 'checked');
            } else {
                checkAll.removeProp('checked');
            }
            checkAll.iCheck('update');
        });
//    });
    //jvectormap data
    var registeredUserLocation = <?=User::jsonUserLocation();?>;



</script>

<?php
echo Hooks::run('admin_footer_action', $data);

?>

</body>
</html>