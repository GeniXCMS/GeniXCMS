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
    <?=Hooks::run('admin_page_top_action', $data);?>
</div>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-dashboard"></i> <?=DASHBOARD;?>
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <?=Hooks::run('admin_page_dashboard_action', $data);?>

        <div class="col-lg-3 col-md-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?=Stats::totalPost('post');?></h3>

                    <p><?=TOTAL_POST;?></p>
                </div>
                <div class="icon">
                    <i class="ion  ion-document"></i>
                </div>
                <a href="<?=Site::$url;?>gxadmin/index.php?page=posts" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?=Stats::totalPost('page');?></h3>

                    <p><?=TOTAL_PAGE;?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-document-text"></i>
                </div>
                <a href="<?=Site::$url;?>gxadmin/index.php?page=pages" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?=Stats::pendingComments();?></h3>

                    <p><?=PENDING.' '.COMMENTS;?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-chatboxes"></i>
                </div>
                <a href="<?=Site::$url;?>gxadmin/index.php?page=comments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?=Stats::totalUser();?></h3>

                    <p><?=TOTAL_USER;?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?=Site::$url;?>gxadmin/index.php?page=users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-md-6 connectedSortable">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-file-text-o"></i> <?=LATEST_POST;?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-condensed">
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Title</th>
                            <th style="width: 60px">Author</th>
                        </tr>
                        <?php
                        $vars = array('num' => 5, 'type' => 'post');
                        $post = Posts::recent($vars);

                        if (isset($post['error'])) {
                            echo "<tr><td colspan='3'>{$post['error']}</td></tr>";
                        } else {
                            foreach ($post as $p) {
                                echo "
                                    <tr>
                                        <td>
                                            {$p->id}
                                        </td>
                                        <td>
                                            <a href=\"".Url::post($p->id)."\" target=\"_blank\">
                                                {$p->title}
                                            </a>
                                        </td>
                                        <td>
                                            <small class=\"badge\">{$p->author}</small>
                                        </td>
                                    </tr>";
                            }
                        }

                        ?>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>

        </div>
        <div class="col-md-6 connectedSortable">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-bar-chart"></i> <?=STATISTIC;?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-condensed">
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Title</th>
                            <th style="width: 60px">Hits</th>
                        </tr>
                        <?php
                        $list = Stats::mostViewed(5);

                        if (isset($list['error'])) {
                            echo "<tr><td colspan='3'>{$list['error']}</td></tr>";
                        } else {
                            foreach ($list as $p) {
                                echo "
                                    <tr>
                                        <td>
                                            {$p->id}
                                        </td>
                                        <td>
                                            <a href=\"".Url::post($p->id)."\" target=\"_blank\">
                                                {$p->title}
                                            </a>
                                        </td>
                                        <td>
                                            <small class=\"badge\">{$p->views}</small>
                                        </td>
                                    </tr>";
                            }
                        }

                        ?>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>

        </div>



        <div class="col-md-6 connectedSortable">
            <!-- USERS LIST -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Latest Members</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <?=User::listRecentBox(8);?>
                    <!-- /.users-list -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    <a href="index.php?page=users" class="uppercase">View All Users</a>
                </div>
                <!-- /.box-footer -->
            </div>
            <!--/.box -->
        </div>
        <!-- /.col -->


        <div class="col-md-6 connectedSortable">
            <!-- Map box -->
            <div class="box box-solid bg-light-blue-gradient">
                <div class="box-header">
                    <!-- tools box -->
                    <div class="pull-right box-tools">

                    </div>
                    <!-- /. tools -->

                    <i class="fa fa-map-marker"></i>

                    <h3 class="box-title">
                        Registered Users Location
                    </h3>
                </div>
                <div class="box-body">
                    <div id="world-map" style="height: 250px; width: 100%;"></div>
                </div>
                <!-- /.box-body-->

            </div>
            <!-- /.box -->
        </div>

    </div>

</section>

