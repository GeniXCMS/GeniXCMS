<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
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
        <h2><i class="fa fa-group"></i> <?=USERS;?>
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#adduser">
                <span class="glyphicon glyphicon-plus"></span> 
                <span class="hidden-xs hidden-sm"><?=ADD_USER;?></span>
            </button>
        </h2>
        <hr />
    </div>
    <div class="col-sm-12">
    <?=$data['paging'];?>
        <form action="index.php?page=users" method="get">
            <input type="hidden" name="page" value="users">
            <div class="row">
                <div class="col-sm-12">
                    <h5><?=FIND_USER;?></h5>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_USER;?>">
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
                            <option value="1"><?=ACTIVE;?></option>
                            <option value="0"><?=INACTIVE;?></option>

                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-search"></span> <?=FIND_USER;?>
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="token" value="<?=TOKEN;?>">
        </form>
    </div>
    <div class="col-sm-12">
    <div class="table-responsive">
    <form action="index.php?page=users" method="post">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?=ID;?></th>
                <th><?=USERNAME;?></th>
                <th><?=EMAIL;?></th>
                <th><?=STATUS;?></th>
                <th><?=GROUP;?></th>
                <th><?=JOIN_DATE;?></th>
                <th><?=ACTION;?></th>
                <th><?=ALL;?> <input type="checkbox" id="selectall"></th>
            </tr>
        </thead>
        <tbody>
            <?php
                //print_r($data);
            if ($data['num'] > 0) {
                foreach ($data['usr'] as $p) {
                    # code...
                    //echo $p->id;

                    if ($p->group == 0) {
                        $grp = ADMINISTRATOR;
                    } elseif ($p->group == 1) {
                        $grp = SUPERVISOR;
                    } elseif ($p->group == 2) {
                        $grp = EDITOR;
                    } elseif ($p->group == 3) {
                        $grp = AUTHOR;
                    } elseif ($p->group == 4) {
                        $grp = GENERAL_MEMBER;
                    }
                    if ($p->status == 0) {
                        $status = "<a href=\"index.php?page=users&act=active&id={$p->id}&token=".TOKEN.'" class="label label-danger">Inactive</a>';
                    } elseif ($p->status == 1) {
                        $status = "<a href=\"index.php?page=users&act=inactive&id={$p->id}&token=".TOKEN.'" class="label label-primary">Active</a>';
                    }

                    echo "
                    <tr>
                        <td>{$p->id}</td>
                        <td>{$p->userid}</td>
                        <td>{$p->email}</td>
                        <td>{$status}</td>
                        <td>{$grp}</td>
                        <td>".Date::format($p->join_date)."</td>
                        <td>
                            <a href=\"index.php?page=users&act=edit&id={$p->id}&token=".TOKEN.'" class="label label-success">'.EDIT."</a>
                            <a href=\"index.php?page=users&act=del&id={$p->id}&token=".TOKEN."\" class=\"label label-danger\"
                            onclick=\"return confirm('".DELETE_CONFIRM."');\">".DELETE."</a>
                        </td>
                        <td>
                            <input type=\"checkbox\" name=\"user_id[]\" value=\"{$p->id}\" id=\"select\">
                        </td>
                    </tr>
                    ";
                }
            }
            ?>
            <tfoot>
                <th><?=ID;?></th>
                <th><?=USERNAME;?></th>
                <th><?=EMAIL;?></th>
                <th><?=STATUS;?></th>
                <th><?=GROUP;?></th>
                <th><?=DATE;?></th>
                <th >
                <select name="action" class="form-control">
                    <option value="activate"><?=ACTIVATE;?></option>
                    <option value="deactivate"><?=DEACTIVATE;?></option>
                    <option value="delete"><?=DELETE;?></option>
                </select>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                </th>
                <th>
                    <button type="submit" name="doaction" class="btn btn-danger btn-sm">
                        <span class="glyphicon glyphicon-ok"></span>
                    </button>
                </th>
            </tfoot>
        </tbody>
    </table>
    </form>
    <?=$data['paging'];?>
    </div>
    </div>
</div>
<!-- Modal -->
    <div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=users" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?=ADD_USER;?></h4>
          </div>
          <div class="modal-body">

                <div class="form-group">
                    <label><?=USERNAME;?></label>
                    <input type="text" name='userid' class="form-control">
                </div>
                <div class="form-group">
                    <label><?=PASSWORD;?></label>
                    <input type="password" name="pass1" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=RETYPE_PASSWORD;?></label>
                    <input type="password" name="pass2" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=EMAIL;?></label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=GROUP;?></label>
                    <select name="group" class="form-control">
                        <option value="0"><?=ADMINISTRATOR;?></option>
                        <option value="1"><?=SUPERVISOR;?></option>
                        <option value="2"><?=EDITOR;?></option>
                        <option value="3"><?=AUTHOR;?></option>
                        <option value="4" selected="on"><?=GENERAL_MEMBER;?></option>
                    </select>
                </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?=CLOSE;?></button>
            <button type="submit" class="btn btn-success" name="adduser"><?=SUBMIT;?></button>
          </div>
          <input type="hidden" name="token" value="<?=TOKEN;?>">
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
