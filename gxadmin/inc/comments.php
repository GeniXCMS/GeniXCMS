<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20160830
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2><i class="fa fa-comments"></i> <?=COMMENTS;?>
        
        </h2>
        <small class="label label-default pull-left"><?=Stats::totalComments();?> total</small>
        <small class="label label-success pull-left"><?=Stats::activeComments();?> active</small>
        <small class="label label-warning pull-left"><?=Stats::pendingComments();?> pending</small>
        <small class="label label-danger pull-left"><?=Stats::inactiveComments();?> inactive</small>
        <hr />
    </div>

    <div class="col-sm-12">
    <?=$data['paging'];?>
        <form action="index.php?page=comments" method="get">
            <input type="hidden" name="page" value="comments">
            <div class="row">
                <div class="col-sm-12">
                    <h5><?=FIND_COMMENTS;?></h5>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_COMMENTS;?>">
                    </div>

                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <div class='input-group date' id='dateFrom'>
                            <input type='text' class="form-control" name="from" placeholder="From" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <div class='input-group date' id='dateTo'>
                            <input type='text' class="form-control" name="to" placeholder="To" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="1"><?=PUBLISHED;?></option>
                            <option value="2"><?=PENDING;?></option>
                            <option value="0"><?=UNPUBLISHED;?></option>

                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
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
    <table class="table table-hover">
        <form action="" method="post">
        <thead>
            <tr>
                <th><?=ID;?></th>
                <th><?=COMMENTS;?></th>
                <th><?=EMAIL;?></th>
                <th><?=DATE;?></th>
                <th><?=STATUS;?></th>
                <th><?=AUTHOR;?></th>
                <th><?=ACTION;?></th>
                <th><?=ALL;?> <input type="checkbox" id="selectall"></th>
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
                    } elseif ($p->status == '1') {
                        $status = PUBLISHED;
                    } elseif ($p->status == '2') {
                        $status = PENDING;
                    }
                    $comment = (strlen($p->comment) > 60) ? substr($p->comment, 0, 58).'...' : $p->comment;
                    echo "
                        <tr>
                            <td>{$p->id}</td>
                            <td><a href=\"".Url::post($p->post_id).'" target="_new" data-toggle="tooltip" data-html="true" title="'.Typo::strip($p->comment).'">'.Typo::strip($comment).'</a><br /><small>IP: '.$p->ipaddress.'</small></td>
                            <td>'.$p->email.'</td>
                            <td>'.Date::format($p->date, 'd M Y')."</td>
                            <td>{$status}</td>
                            <td>{$p->userid}</td>
                            <td>
                                <a href=\"index.php?page=comments&act=del&id={$p->id}&token=".TOKEN."\" class=\"label label-danger\"
                                onclick=\"return confirm('Are you sure you want to delete this item?');\">".DELETE."</a>
                            </td>
                            <td>
                                <input type=\"checkbox\" name=\"post_id[]\" value=\"{$p->id}\" id=\"select\">
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
            <th><?=EMAIL;?></th>
            <th><?=DATE;?></th>
            <th><?=STATUS;?></th>
            <th><?=AUTHOR;?></th>
            <th >
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
        </form>
    </table>
    <?=$data['paging'];?>
    </div>
    </div>

</div>
