
            <br />
            <footer class="footer">
                <hr />
                <?php
                    $end_time = microtime(true);
                    $time_taken = $end_time - $GLOBALS['start_time'];
                    $time_taken = round($time_taken, 5);
                    echo '<small>Page generated in '.$time_taken.' seconds.</small> <br />';
                ?>
                <small>Copyright &copy; <?=date('Y');
?> <a href="http://genixcms.org">GeniXCMS</a> <i><?=System::v();?></i></small>
            </footer>
        </div>

    </div>

    


    



    <?php
    if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
        Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));

        $url = (SMART_URL) ? Site::$url.'/ajax/saveimage?token='.TOKEN : Site::$url.'/index.php?ajax=saveimage&token='.TOKEN;
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

        $(\'#content\').each(function(i, obj) { $(obj).summernote({
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
      echo Hooks::run('admin_footer_action', $data);
    ?>

    <script>
        $("#selectall").change(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
              //alert(cb.val());
        });
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
            })
        });

        <?php
        $tagAjax = (SMART_URL) ? Site::$url.'ajax/tags?token='.TOKEN : Site::$url.'index.php?ajax=tags&token='.TOKEN;
        $versionUrl = (SMART_URL) ? Site::$url.'ajax/version?token='.TOKEN : Site::$url.'index.php?ajax=version&token='.TOKEN;
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

        

    </script>

  </body>
</html>