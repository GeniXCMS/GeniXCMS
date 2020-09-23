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
        <h1><i class="fa fa-group"></i> <?=USERS;?>
            <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#adduser">
                <span class="glyphicon glyphicon-plus"></span> 
                <span class="hidden-xs hidden-sm"><?=ADD_USER;?></span>
            </button>
        </h1>

    </section>
<section class="content">
    <!-- Default box -->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">
                <small class="label label-default pull-left"><?=Stats::totalUser();?> total</small>
                <small class="label label-success pull-left"><?=Stats::activeUser();?> active</small>
                <small class="label label-warning pull-left"><?=Stats::pendingUser();?> pending</small>
                <small class="label label-danger pull-left"><?=Stats::inactiveUser();?> inactive</small>
            </h3>

            <div class="box-tools pull-right">
                <?=$data['paging'];?>
            </div>
        </div>
        <div class="box-body">
    <div class="row">
    <div class="col-sm-12">
        <form action="index.php?page=users" method="get">
            <input type="hidden" name="page" value="users">
            <div class="row">
                <div class="col-sm-12">
                    <h5><?=FIND_USER;?></h5>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_USER;?>">
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
                    <?php
                    $var = array('name' => 'group');
                    echo User::dropdown($var);
                    ?>
                </div>
                </div>
                <div class="col-sm-6 col-md-2">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="1"><?=ACTIVE;?></option>
                            <option value="0"><?=INACTIVE;?></option>

                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-1">
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
                <th><?=COUNTRY;?></th>
                <th><?=ACTION;?></th>
                <th><?=ALL;?> <input type="checkbox" id="selectall"></th>
            </tr>
        </thead>
        <tbody>
            <?php
                //print_r($data);
            if ($data['num'] > 0) {
                foreach ($data['usr'] as $p) {
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
                        $grp = CONTRIBUTOR;
                    } elseif ($p->group == 5) {
                        $grp = VIP_MEMBER;
                    } elseif ($p->group == 6) {
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
                        <td class='text-center'><span class='flag-icon flag-icon-".strtolower($p->country)."'></span></td>
                        <td>
                            <a href=\"index.php?page=users&act=edit&id={$p->id}&token=".TOKEN.'" class="label label-success"><i class="fa fa-edit"></i></a>
                            <a href="index.php?page=users&act=del&id='.$p->id.'&token='.TOKEN."\" class=\"label label-danger\"
                            onclick=\"return confirm('".DELETE_CONFIRM."');\"><i class=\"fa fa-remove\"></i></a>
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
                <th><?=JOIN_DATE;?></th>
                <th colspan="2">
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
                    <?php
                    $var = array('name' => 'group', 'selected' => '6', 'update' => true);
                    echo User::dropdown($var);
                    ?>
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
