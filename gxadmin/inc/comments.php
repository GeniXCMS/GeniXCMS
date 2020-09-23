<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20160830
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
        <h1><i class="fa fa-comments"></i> <?=COMMENTS;?>
            <small>
                Comments List
            </small>
        </h1>

    </section>
    <section class="content">
        <!-- Default box -->
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <small class="label label-default pull-left"><?=Stats::totalComments();?> total</small>
                    <small class="label label-success pull-left"><?=Stats::activeComments();?> active</small>
                    <small class="label label-warning pull-left"><?=Stats::pendingComments();?> pending</small>
                    <small class="label label-danger pull-left"><?=Stats::inactiveComments();?> inactive</small>
                </h3>

                <div class="box-tools pull-right">
                    <?=$data['paging'];?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">

                        <form action="index.php?page=comments" method="get">
                            <input type="hidden" name="page" value="comments">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5><?=FIND_COMMENTS;?></h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_COMMENTS;?>">
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
                                            <option value="2"><?=PENDING;?></option>
                                            <option value="0"><?=UNPUBLISHED;?></option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <span class="glyphicon glyphicon-search"></span> <?=FIND_COMMENTS;?>
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
                                    <th class="text-center"><?=ID;?></th>
                                    <th class="text-center col-md-5"><?=COMMENTS;?></th>
                                    <th class="text-center col-md-1"><?=EMAIL;?></th>
                                    <th class="text-center col-md-1"><?=DATE;?></th>
                                    <th class="text-center col-md-1"><?=STATUS;?></th>
                                    <th class="text-center col-md-1"><?=AUTHOR;?></th>
                                    <th class="text-center col-md-1"><?=ACTION;?></th>
                                    <th class="text-center col-md-1"><?=ALL;?> <input type="checkbox" id="selectall" class="all"></th>
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
                                        } elseif ($p->status == '1') {
                                            $status = PUBLISHED;
                                            $trclass = '';
                                        } elseif ($p->status == '2') {
                                            $status = PENDING;
                                            $trclass = 'bg-danger';
                                        }
                                        $comment = (strlen($p->comment) > 60) ? substr($p->comment, 0, 58).'...' : $p->comment;
                                        echo "
                                            <tr class=\"$trclass\">
                                                <td class=\"text-center\">{$p->id}</td>
                                                <td><a href=\"".Url::post($p->post_id).'" target="_new">'.Typo::strip($comment).'</a><br /><small>IP: '.$p->ipaddress.'</small></td>
                                                <td class="text-center">'.$p->email.'</td>
                                                <td class="text-center">'.Date::format($p->date, 'd M Y').'<br /><small>'.Date::format($p->date, 'H:i A')."</small></td>
                                                <td class=\"text-center\">{$status}</td>
                                                <td class=\"text-center\">{$p->name}</td>
                                                <td class=\"text-center\">
                                                    <a href=\"index.php?page=comments&act=del&id={$p->id}&token=".TOKEN."\" class=\"label label-danger\"
                                                    onclick=\"return confirm('Are you sure you want to delete this item?');\"><i class=\"fa fa-remove\"></i></a>
                                                </td>
                                                <td class=\"text-center\">
                                                    <input type=\"checkbox\" name=\"post_id[]\" value=\"{$p->id}\" id=\"select\" class='all'>
                                                </td>
                                            </tr>
                                            ";
                                    }
                                } else {
                                    echo '
                                        <tr>
                                            <td colspan="8">
                                            '.NO_POST_FOUND.'
                                            </td>
                                        </tr>';
                                }
                                ?>

                            </tbody>
                            <tfoot>
                                <th class="text-center"><?=ID;?></th>
                                <th class="text-center"><?=COMMENTS;?></th>
                                <th class="text-center"><?=EMAIL;?></th>
                                <th class="text-center"><?=DATE;?></th>
                                <th class="text-center"><?=STATUS;?></th>
                                <th class="text-center" colspan="2">
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