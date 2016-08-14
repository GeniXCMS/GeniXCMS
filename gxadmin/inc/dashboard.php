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
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2><i class="fa fa-dashboard"></i> <?=DASHBOARD;?></h2>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="row">
            <?=Hooks::run('admin_page_dashboard_action', $data);?>
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
                                # code...
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
                            echo '<li class="list-group-item">'.TOTAL_POST.': '.Stats::totalPost('post').'</li>'
                                .'<li class="list-group-item">'.TOTAL_PAGE.': '.Stats::totalPost('page').'</li>'
                                .'<li class="list-group-item">'.TOTAL_CAT.': '.Stats::totalCat('post').'</li>'
                                .'<li class="list-group-item">'.TOTAL_USER.': '.Stats::totalUser().'</li>';
                            echo Hooks::run('admin_page_dashboard_statslist_action', $data);
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
