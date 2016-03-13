        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
            <?=Language::flagList();?>
            <div class="sidebar-module sidebar-module-inset">
                <h4>About</h4>
                <h5><?=Site::logo('','40px');?> <?=Site::$name;?></h5>
                <p><em><?=Options::v('siteslogan');?></em>
                    <?=Site::$desc;?></p>
                </div>
                <div class="sidebar-module">
                    <h4>Recent Post</h4>
                    <ol class="list-unstyled">
                        <?php
                        $rcnt = array(
                            'num' => 10
                        );
                        $recent = Posts::recent($rcnt);
                        $num = count($recent);
                        if($num > 0) {
                            foreach ($recent as $r) {
                      # code...
                                echo "<li><a href=\"".Url::post($r->id)."\">$r->title</a></li>
                                ";
                            }
                        }else{
                            echo "No Post to Show";
                        }
                        $vars = array(
                            'num' => 10,
                            'excerpt' => true,
                            'class' => array(
                                'ul' => 'list-group',
                                'li' => 'list-group-item'
                            )
                        );
                        Posts::lists($vars);
                        ?>
                    </ol>
                </div>
                <div class="sidebar-module">
                    <h4>Elsewhere</h4>
                    <ol class="list-unstyled">
                        <li><a href="#">GitHub</a></li>
                        <li><a href="#">Twitter</a></li>
                        <li><a href="#">Facebook</a></li>
                    </ol>
                </div>
            </div><!-- /.blog-sidebar -->
