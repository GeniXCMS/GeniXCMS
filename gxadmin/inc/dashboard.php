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
 * @copyright 2014-2017 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2><i class="fa fa-dashboard"></i> <?=DASHBOARD;?></h2>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="row">
            <?=Hooks::run('admin_page_dashboard_action', $data);?>
            
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-file-text-o fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?=Stats::totalPost('post');?></div>
                                <div><?=TOTAL_POST;?></div>
                            </div>
                        </div>
                    </div>
                    <a href="<?=Site::$url;?>gxadmin/index.php?page=posts">
                        <div class="panel-footer">
                            <span class="pull-left">View Posts</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-file-text fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?=Stats::totalPost('page');?></div>
                                <div><?=TOTAL_PAGE;?></div>
                            </div>
                        </div>
                    </div>
                    <a href="<?=Site::$url;?>gxadmin/index.php?page=pages">
                        <div class="panel-footer">
                            <span class="pull-left">View Pages</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-comments fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?=Stats::pendingComments();?></div>
                                <div><?=PENDING.' '.COMMENTS;?></div>
                            </div>
                        </div>
                    </div>
                    <a href="<?=Site::$url;?>gxadmin/index.php?page=comments">
                        <div class="panel-footer">
                            <span class="pull-left">View Comments</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-users fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?=Stats::totalUser();?></div>
                                <div><?=TOTAL_USER;?></div>
                            </div>
                        </div>
                    </div>
                    <a href="<?=Site::$url;?>gxadmin/index.php?page=users">
                        <div class="panel-footer">
                            <span class="pull-left">View Users</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-file-text-o"></i> <?=LATEST_POST;?></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                        <?php
                        $vars = array('num' => 5, 'type' => 'post');
                        $post = Posts::recent($vars);

                        if (isset($post['error'])) {
                            echo "<li class=\"list-group-item\">{$post['error']}</li>";
                        } else {
                            foreach ($post as $p) {
                                echo '
                                        <li class="list-group-item">
                                            <a href="'.Url::post($p->id)."\" target=\"_blank\">
                                                $p->title
                                            </a>
                                            <small class=\"badge\">$p->author</small>

                                        </li>";
                            }
                        }

                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?=STATISTIC;?></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                        <?php

                        // print_r(Stats::mostViewed(5));
                        $list = Stats::mostViewed(5);
                        echo '<ul class="list-group">';
                        if (!isset($list['error'])) {
                            foreach ($list as $p) {
                                echo '<li class="list-group-item"><a href="'.Url::post($p->id).'" target="_blank">'.$p->title.'</a><span class="badge pull-right" data-toggle="tooltip" title="views">'.$p->views.'</span></li>';
                            }
                        } else {
                            echo '<li  class="list-group-item">No Post to Show</li>';
                        }
                        echo '</ul>';

                        echo Hooks::run('admin_page_dashboard_statslist_action', $data);

                        ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
