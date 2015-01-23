<div class="col-sm-12 blog-main">
    <div class="row clearfix">
        <div class="col-sm-12">
        <form action="" method="get" class="form-inline">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        Find :
                    </span>
                    <input type="text" name="query" class="form-control" placeholder="Title/Keywords">
                </div>
            </div>
            <div class="form-group">
                <select name="genre" class="form-control">
                    <option value="">Genres :</option>
                    <option>Action</option>
                    <option>Adventure</option>
                    <option>Comedy</option>
                    <option>Drama</option>
                    <option>Family</option>
                    <option>Romance</option>
                    <option>Crime</option>
                    <option>Mystery</option>
                    <option>Horror</option>
                    <option>Thriller</option>
                </select>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        Year :
                    </span>

                    <select name="yearmin" class="form-control">
                        <option value="">Min :</option>
                        <?php
                            for($i=1940;$i<date('Y');$i+=5){
                                echo "<option>{$i}</option>";
                            }
                        ?>
                    </select>
                </div>
                    <select name="yearmax" class="form-control">
                        <option value="">Max :</option>
                        <?php
                            for($i=date('Y');$i>1940;$i-=5){
                                echo "<option>{$i}</option>";
                            }
                        ?>
                    </select>
                
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        Rating :
                    </span>

                    <select name="ratingmin" class="form-control">
                        <option value="">Min :</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                </div>
                    <select name="ratingmax" class="form-control">
                        <option value="">Max :</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                
            </div>
            <div class="form-group">
                <button name="search" type="submit" class="btn btn-warning">
                    Search
                </button>
            </div>
        </form>
        </div>
    </div>
    <div class="clearfix">
        <br />
    </div>
<?php
    echo "<div class=\"row clearfix\">";
    // echo "<pre>";
    // print_r($data['posts']);
    // echo "</pre>";
    if(!isset($data['posts']['error'])){
        foreach ($data['posts'] as $p) {
            # code...
            //print_r($p->poster);
            $param = json_decode(Typo::Xclean($p->value), true);
            // echo "<pre>";
            // var_dump($param);
            // echo "</pre>";
            $imgdesc = substr($p->content, 0, 150);
            $title = substr($p->title, 0, 24);
            echo "
            <div class=\"item col-xs-6 col-sm-4 col-md-3\">
                <a href=\"".Url::post($p->post_id)."\">
                <div class=\"img-thumbnail center-block\">
                    <img src=\"{$p->poster}\" class=\"img-responsive center-block\" title=\"{$p->title}\" alt=\"{$imgdesc}...\" rel=\"tooltip\">
                    <div class=\"item-title\">
                        {$title}
                    </div>
                    
                </div>
                </a>
                <div class=\"input-group\">
                    <span class=\"form-control\">
                        Rating : {$p->rating}
                    </span>
                    <span class=\"input-group-addon\">
                        {$p->votes} Vote(s)
                    </span>

                </div>
            </div>
                ";
        }
    }else{
        echo "No Movies Found";
    }
    

    echo "</div>";
    echo $data['paging'];
?>
</div>
<?php //Theme::theme('rightside', $data); ?>