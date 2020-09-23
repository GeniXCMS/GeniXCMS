<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
if (isset($_GET['token'])
    && Token::validate($_GET['token'])) {
    $token = TOKEN;
} else {
    $token = '';
}
($_GET['act'] == 'edit') ? $pagetitle = 'Edit' : $pagetitle = 'New';
($_GET['act'] == 'edit') ? $act = "edit&id=".Typo::int($_GET['id'])."&token=".$token : $act = 'add';

if (isset($data['post'])) {
    if (!isset($data['post']['error'])) {
        //print_r($data['post']);
        foreach ($data['post'] as $p) {
            $title = $p->title;
            $content = $p->content;
            $date = $p->date;
            $status = $p->status;
            $cat = $p->cat;
            $tags = @$p->tags;
        }
        if ($status == 1) {
            $pub = 'SELECTED';
            $unpub = '';
        } elseif ($status == 0) {
            $pub = '';
            $unpub = 'SELECTED';
        }
        $id = Typo::int($_GET['id']);
    } else {
        $title = '';
        $content = '';
        $date = '';
        $status = '';
        $cat = '';
        $pub = '';
        $unpub = '';
        $tags = '';
        $data['alertDanger'][] = $data['post']['error'];
    }
} else {
    $title = '';
    $content = '';
    $date = '';
    $status = '';
    $cat = '';
    $pub = '';
    $unpub = '';
    $tags = '';
}

?>
<form action="index.php?page=pages&act=<?=$act?>" method="post" role="form" class="">

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <section class="content-header">
        <h1><i class="fa fa-file-o"></i> <?=$pagetitle;
?> <?=PAGE;?>
            <div class="pull-right">

            </div>
        </h1>
    </section>
    <section class="content">
        <!-- Default box -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Add Page
                </h3>

                <div class="box-tools pull-right">
                    <button type="submit" name="submit" class="btn btn-success btn-sm">
                        <span class="glyphicon glyphicon-ok"></span>
                        <span class="hidden-xs hidden-sm"><?=SUBMIT;?></span>
                    </button>

                    <a href="index.php?page=pages" class="btn btn-danger btn-sm">
                        <span class="glyphicon glyphicon-remove"></span>
                        <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
                    </a>
                </div>
            </div>
            <div class="box-body">
        <div class="row">

                <div class="col-sm-8" id="myTab">
                    <?php
                    if (Options::v('multilang_enable') === 'on') {
                        $def = Options::v('multilang_default');
                        $deflang = Language::getDefaultLang();
                        $listlang = json_decode(Options::v('multilang_country'), true);
                        $deflag = strtolower($listlang[$def]['flag']);

                        echo "<div class='nav-tabs-custom'>
                    <ul class=\"nav nav-tabs\" role=\"tablist\">
                        <li class=\"active\"><a href=\"#lang-{$def}\" role=\"tab\" data-toggle=\"tab\"><span class=\"flag-icon flag-icon-{$deflag}\"></span> {$deflang['country']}</a></li>";

                        unset($listlang[Options::v('multilang_default')]);
                        foreach ($listlang as $key => $value) {
                            $flag = strtolower($value['flag']);
                            echo "
                        <li><a href=\"#lang-{$key}\" role=\"tab\" data-toggle=\"tab\"><span class=\"flag-icon flag-icon-{$flag}\"></span> {$value['country']}</a></li>";
                        }

                        echo "
                    </ul>
                    <div class=\"clearfix\">&nbsp;</div>
                    <div class=\"tab-content\">
                    <!-- Tab Pane General -->
                    <div class=\"tab-pane active\" id=\"lang-{$def}\">
                        <div class=\"form-group\">
                            <label for=\"title\">".TITLE." ({$def}) </label>
                            <input type=\"title\" name=\"title[{$def}]\" class=\"form-control\" id=\"title\" placeholder=\"Post Title\" value=\"{$title}\">
                        </div>
                        <div class=\"form-group\">
                            <label for=\"content\">".CONTENT." </label> <a href=\"#\" id=\"toggleEditor\" class=\"btn btn-danger btn-xs pull-right\"><i class=\"fa fa-desktop\"></i> Editor</a>
                            
                            <textarea name=\"content[{$def}]\" class=\"form-control content editor ge-html-output\" id=\"content\" rows=\"20\">{$content}</textarea>
                            <div id=\"myGrid\">{$content}</div>
                        </div>
                    </div>
                    ";
                        unset($listlang[Options::v('multilang_default')]);
                        foreach ($listlang as $key => $value) {
                            if (isset($_GET['act']) && $_GET['act'] == 'edit') {
                                $lang = Language::getLangParam($key, $id);
                                if ($lang == '' || !Posts::existParam('multilang', $id)) {
                                    $lang['title'] = $title;
                                    $lang['content'] = $content;
                                } else {
                                    $lang = $lang;
                                }
                            } else {
                                $lang['title'] = '';
                                $lang['content'] = '';
                            }
                            echo "
                    <div class=\"tab-pane\" id=\"lang-{$key}\">
                    
                        <div class=\"form-group\">
                            <label for=\"title\">".TITLE." ({$key}) </label>
                            <input type=\"title\" name=\"title[{$key}]\" class=\"form-control\" id=\"title\" placeholder=\"Post Title\" value=\"{$lang['title']}\">
                        </div>
                        <div class=\"form-group\">
                            <label for=\"content\">".CONTENT."</label> 
                            <textarea name=\"content[{$key}]\" class=\"form-control content editor\" id=\"content_{$key}\" rows=\"20\">{$lang['content']}</textarea>
                        </div>
                    </div>
                    
                        ";
                            $asset = '<script>
                                $(document).ready(function(){
                                    // $(\'.tab-content\').each(function(){
                                    //     $(\'#myGrid_'.$key.'\').gridEditor({
                                    //         content_types: [\'summernote\'],
                                    //         source_textarea: \'#content_'.$key.'\'
                                    //     });
                                    // });
                                    // $(\'#myGrid_'.$key.'\').hide();
                                    // $(".ge-mainControls").hide();
                                    // $(\'#toggleEditor_'.$key.'\').click(
                                    //     function(){
                                    //         $(\'#myGrid_'.$key.'\').toggle();
                                    //         $(".note-editor").toggle();
                                    //         $(".ge-mainControls").toggle();
                                    //     }
                                    // );

                                    $(\'#content_'.$key.'\').each(function(i, obj) { $(obj).summernote({
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
                            System::adminAsset($asset);

                            unset($lang);
                        }

                        echo '</div></div>';
                    } else {
                        ?>
                        <div class="form-group">
                        <label for="title"><?=TITLE; ?></label>
                        <input type="title" name="title" class="form-control" id="title" placeholder="Post Title" value="<?=$title; ?>">
                        </div>
                        <div class="form-group">
                        <label for="content"><?=CONTENT; ?></label> <a href="#" id="toggleEditor" class="btn btn-danger btn-xs pull-right"><i class="fa fa-desktop"></i> Editor</a>
                        <textarea name="content" class="form-control content editor" id="content" rows="20"><?=$content; ?></textarea>
                        <div id="myGrid"><?=$content; ?></div>
                        </div>
                        <?php

                    }
                    Hooks::run('page_param_form', $data);
                ?>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?=OPTIONS;?></h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label><?=STATUS;?></label>
                                <select name="status" class="form-control">
                                    <option value="1" <?=$pub;
?>><?=PUBLISH;?></option>
                                    <option value="0" <?=$unpub;
?>><?=UNPUBLISH;?></option>
                                </select>
                                <small><?=PUBLISHED;
?> or <?=UNPUBLISHED;?></small>
                            </div>

                            <div class="form-group">
                                <label><?=POST_DATE;?></label>
                                <div class='input-group date' id='dateTime'>
                                    <input type='text' class="form-control" name="date" value="<?=$date;?>" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <small><?=LEFT_IT_BLANK_NOW_DATE;?></small>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?=TAGS;?></h3>
                        </div>
                        <div class="panel-body">
                            <textarea name="tags" class="form-control"><?=$tags;?></textarea>
                            <small><?=TAGS_DESC;?></small>
                        </div>
                    </div> -->
                </div>

        </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">

        </div>
        <!-- /.box-footer-->
        </div>
        <!-- /.box -->
    </section>

<input type="hidden" name="token" value="<?=$token;?>">
</form>
