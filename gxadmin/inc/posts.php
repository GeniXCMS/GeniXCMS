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
?>

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> <?=POSTS;?>
            <a href="index.php?page=posts&act=add&token=<?=TOKEN;?>"
            class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-plus"></i> 
                <span class="hidden-xs hidden-sm"><?=ADD_NEW_POST;?></span>
            </a>
        </h1>
    </section>
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <small class="label label-default pull-left"><?=Stats::totalPost('post');?> total</small>
                    <small class="label label-success pull-left"><?=Stats::activePost('post');?> active</small>
                    <small class="label label-danger pull-left"><?=Stats::inactivePost('post');?> inactive</small>
                </h3>

                <div class="box-tools pull-right">
                    <?=$data['paging'];?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
        <div class="col-sm-12">
            <form action="index.php?page=posts" method="get">
                <input type="hidden" name="page" value="posts">
                <div class="row">
                    <div class="col-md-12">
                        <h5><?=FIND_POSTS;?></h5>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_POSTS;?>">
                        </div>

                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <?php
                            $vars = array(
                                        'name' => 'cat',
                                        'type' => 'post',
                                        );
                            echo Categories::dropdown($vars);
                        ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                            <div class='input-group date' id='dateFrom'>
                                <input type='text' class="form-control" name="from" placeholder="From" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                            <div class='input-group date' id='dateTo'>
                                <input type='text' class="form-control" name="to" placeholder="To" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="1"><?=PUBLISHED;?></option>
                                <option value="0"><?=UNPUBLISHED;?></option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <span class="glyphicon glyphicon-search"></span> <?=FIND_POSTS;?>
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
        <div class="col-sm-12">

        <div class="table-responsive">
        <form action="" method="post">
        <table class="table table-hover">
            
            <thead>
                <tr>
                    <th><?=ID;?></th>
                    <th><?=TITLE;?></th>
                    <th  class="text-center"><?=CATEGORY;?></th>
                    <th class="text-center"><?=DATE;?></th>
                    <th class="text-center"><?=STATUS;?></th>
                    <th class="text-center"><?=AUTHOR;?></th>
                    <th class="text-center col-md-1"><?=ACTION;?></th>
                    <th class="text-center"><?=ALL;?> <input type="checkbox" id="selectall" class="all"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    //print_r($data);
                if ($data['num'] > 0) {
                    foreach ($data['posts'] as $p) {
                        //print_r($p);
                        //echo $p->id;
                        if ($p->status == '0') {
                            $status = UNPUBLISHED;
                            $trclass = 'bg-warning';
                        } else {
                            $trclass = '';
                            $status = PUBLISHED;
                        }
                        $title = (strlen($p->title) > 60) ? substr($p->title, 0, 58).'...' : $p->title;
                        echo '
                            <tr class="'.$trclass."\">
                                <td>{$p->id}</td>
                                <td><a href=\"".Url::post($p->id)."\" target=\"_new\">{$title}</a></td>
                                <td  class=\"text-center\">".Categories::name($p->cat).'</td>
                                <td class="text-center">'.Date::format($p->date, 'd M Y').'<br /><small>'.Date::format($p->date, 'H:i A')."</small></td>
                                <td  class=\"text-center\">{$status}</td>
                                <td  class=\"text-center\">{$p->author}</td>
                                <td class=\"text-center\">
                                    <a href=\"index.php?page=posts&act=edit&id={$p->id}&token=".TOKEN.'" class="label label-success"><i class="fa fa-edit"></i></a>
                                    <a href="index.php?page=posts&act=del&id='.$p->id.'&token='.TOKEN."\" class=\"label label-danger\"
                                    onclick=\"return confirm('".DELETE_CONFIRM."');\"><i class=\"fa fa-remove\"></i></a>
                                </td>
                                <td class=\"text-center\">
                                    <input type=\"checkbox\" name=\"post_id[]\" value=\"{$p->id}\" id=\"select\" class=\"check\">
                                </td>
                            </tr>
                            ";
                    }
                } else {
                    echo '
                        <tr>
                            <td>
                            '.NO_POST_FOUND.'
                            </td>
                        </tr>';
                }
                ?>

            </tbody>
            <tfoot>
                <th><?=ID;?></th>
                <th><?=TITLE;?></th>
                <th class="text-center"><?=CATEGORY;?></th>
                <th class="text-center"><?=DATE;?></th>
                <th class="text-center"><?=STATUS;?></th>
                <th colspan="2">
                <select name="action" class="form-control">
                    <option value="publish"><?=PUBLISH;?></option>
                    <option value="unpublish"><?=UNPUBLISH;?></option>
                    <option value="delete"><?=DELETE;?></option>
                </select>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                </th>
                <th>
                    <button type="submit" name="doaction" class="btn btn-danger">
                        <span class="glyphicon glyphicon-ok"></span>
                    </button>
                </th>
            </tfoot>
            
        </table>
        </form>
        </div>
        </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <?=$data['paging'];?>
            </div>
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->
    </section>

