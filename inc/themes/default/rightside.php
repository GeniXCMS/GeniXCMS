        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
            <?=Language::flagList();?>
            <div class="sidebar-module sidebar-module-inset">
                <h4>About</h4>
                <h5><?=Site::logo('','40px');?> <?=Site::$name;?></h5>
                <p><em><?=Options::v('siteslogan');?></em>
                    <?=Site::$desc;?></p>
                </div>
                <div class="sidebar-module posts-list">
                    <!-- <h4>Recent Post</h4>
                    <ol class="list-unstyled">
                        <?php
                        // $rcnt = array(
                        //     'num' => 10
                        // );
                        // $recent = Posts::recent($rcnt);
                        // $num = count($recent);
                        // if (!isset($recent['error'])) {
                        //     foreach ($recent as $r) {
                        //         echo "<li><a href=\"".Url::post($r->id)."\">$r->title</a></li>
                        //         ";
                        //     }
                        // } else {
                        //     echo "No post(s) to show";
                        // }

                        ?>
                    </ol> -->
                    <h4>Latest Post</h4>
                    <?php
                        $vars = array(
                            'num' => 5,
                            'title' => true,
                            'excerpt' => true,
                            'excerpt_max' => 50,
                            'class' => array(
                                            'ul' => 'list-group posts-list',
                                            'li' => 'list-group-item'
                                        )
                        );
                        Posts::lists($vars);
                    ?>
                </div>

                    <?php if (mdoTheme::opt('mdo_adsense') != '') {
                        echo mdoTheme::opt('mdo_adsense')."<hr />";
                    } ?>

            </div><!-- /.blog-sidebar -->
