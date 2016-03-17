

            <br />
            <footer class="footer">
                <hr />
                <?php
                    $end_time = microtime(TRUE);
                    $time_taken = $end_time - $GLOBALS['start_time'];
                    $time_taken = round($time_taken,5);
                    echo '<small>Page generated in '.$time_taken.' seconds.</small> <br />';
                ?>
                <small>Copyright &copy; <?=date("Y");?> <a href="http://genixcms.org">GeniXCMS</a> <i><?=System::v();?></i></small>
            </footer>
        </div>

    </div>

    <!-- LOAD CSS -->
    <link href="<?=Site::$url;?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?=Site::$url;?>/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/dashboard.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/genixfont.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/flag-icon.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>/assets/css/jquery.tagsinput.min.css" rel="stylesheet">


    <!-- LOAD Javascript -->
    <script src="<?=Site::$url;?>/assets/js/jquery.min.js"></script>
    <script src="<?=Site::$url;?>/assets/js/bootstrap.min.js"></script>
    <script src="<?=Site::$url;?>/assets/js/moment-locales.min.js"></script>
    <script src="<?=Site::$url;?>/assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?=Site::$url;?>/assets/js/jquery.tagsinput.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=Site::$url;?>/assets/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?=Site::$url;?>/assets/js/sb-admin-2.js"></script>

    <!-- MetisMenu CSS -->
    <link href="<?=Site::$url;?>/assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">



    <?php
      if(isset($GLOBALS['editor']) && $GLOBALS['editor'] == true){
            Hooks::attach('admin_footer_action', array('Files','elfinderLib'));
            if ($GLOBALS['editor_mode'] == 'light') {
                $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore']],
                    ['view', ['fullscreen']]";
            }elseif ($GLOBALS['editor_mode'] == 'full') {
                $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore']],
                    ['genixcms', ['elfinder']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]";
            }

            $url = (SMART_URL)? Site::$url . '/ajax/saveimage?token=' . TOKEN : Site::$url . "/index.php?ajax=saveimage&token=" . TOKEN;
            $foot = "

    <link href=\"".Site::$url."/assets/css/summernote.css\" rel=\"stylesheet\">
    <script src=\"".Site::$url."/assets/js/summernote.min.js\"></script>
    <script src=\"".Site::$url."/assets/js/plugins/summernote-ext-hint.js\"></script>
    <script src=\"".Site::$url."/assets/js/plugins/summernote-ext-video.js\"></script>
    <script src=\"".Site::$url."/assets/js/plugins/summernote-ext-genixcms.js\"></script>
    <script>
      $(document).ready(function() {
        $('.editor').summernote({
            height: 300,
            toolbar: [
                    ".$toolbar."
                ],
            onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0],editor,welEditable);
                }
        });

        function sendFile(file,editor,welEditable) {
          data = new FormData();
          data.append(\"file\", file);
            $.ajax({
                url: \"".$url."\",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                //alert(data);
                  $('.editor').summernote('editor.insertImage', data);
                },
               error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus+\" \"+errorThrown);
               }
            });
          }

         $(\".alert\").alert();
      });


    </script>
              ";
              echo $foot;
        }
      echo Hooks::run('admin_footer_action', $data);
    ?>

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

        $(function () {
            $('#dateFrom').datetimepicker({
                format: 'YYYY/MM/DD'
            });
            $('#dateTo').datetimepicker({
                format: 'YYYY/MM/DD',
                useCurrent: false //Important! See issue #1075
            });
            $("#dateFrom").on("dp.change", function (e) {
                $('#dateTo').data("DateTimePicker").minDate(e.date);
            });
            $("#dateTo").on("dp.change", function (e) {
                $('#dateFrom').data("DateTimePicker").maxDate(e.date);
            });
            $('#dateTime').datetimepicker({
                format: 'YYYY/MM/DD HH:mm:ss',
                useCurrent: true
            })
        });

        <?php
            $tagAjax = (SMART_URL)? Site::$url . '/ajax/tags?token=' . TOKEN : Site::$url . "/index.php?ajax=tags&token=" . TOKEN;
         ?>
        $('#tags').tagsInput({
            width: 'auto',
            autocomplete_url: '<?=$tagAjax;?>',
            autocomplete:{selectFirst:true,width:'100px',autoFill:true}
        });

    </script>

  </body>
</html>
