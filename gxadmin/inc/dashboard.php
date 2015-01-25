<div class="row">
    <div class="col-md-12">
        <h1>Dashboard</h1>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Latest Post</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                        <?php 
                            $post = Posts::recent(5, 'post'); 

                            //print_r($post);
                            foreach ($post as $p) {
                                # code...
                                echo "
                                    <li class=\"list-group-item\">
                                        <a href=\"".Url::post($p->id)."\" target=\"_blank\">
                                            $p->title 
                                        </a>
                                        <small class=\"badge\">$p->author</small>
                                        
                                    </li>";
                            }
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Statistic</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                        <?php 
                            echo "<li class=\"list-group-item\">Total Post: ".Stats::totalPost('post')."</li>"
                                ."<li class=\"list-group-item\">Total Page: ".Stats::totalPost('page')."</li>";
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</div>