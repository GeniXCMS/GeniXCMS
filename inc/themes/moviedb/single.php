<div class=" blog-main" >
<?php
    if(isset($data['posts'][0]->title)){

        foreach ($data['posts'] as $p) {
            # code...
            //var_dump($p->value);
            $title = $p->title;
            $content = Typo::Xclean($p->content);
            $param = json_decode(stripslashes(Typo::Xclean($p->value)), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            //echo(Typo::Xclean($p->value));
            echo "
            <div class=\"container\">
            <div class=\"blog-post\" itemprop=\"video\" itemscope itemtype=\"http://schema.org/VideoObject\">
                <h2 class=\"blog-post-title\" >
                    <a href=\"".Url::post($p->post_id)."\" title=\"Watch Online Free $p->title\" itemprop=\"name\">Watch Online $p->title</a>
                </h2>
                <meta itemprop=\"duration\" content=\"T{$param['runtime']}M\" />
                <meta itemprop=\"thumbnail\" content=\"{$p->backdrop}\" />
                <meta itemprop=\"description\" content=\"{$p->content}\" />
                <div class=\"player\" href=\"".$p->opening."\" 
                style=\"background-image:url(".$p->backdrop.");
                alignt:center;height: 100%;max-height: 480px\">
                
                <img src=\"http://flash.flowplayer.org/media/img/player/btn/play_large.png\" alt=\"Play this video\" class=\"playbutton\" />
                </div>
            </div>
            </div>
                ";
        }
    
    
?>
    <script>
        
        flowplayer("div.player", {src: "http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" },{
            
            clip: {
               
                duration: 8880,
                //Waits for player to reach ten seconds, then pauses
                onCuepoint: [7000, function() {
                    this.pause();

                    this.getPlugin('pleaseLogIn').animate({'opacity': 1});
                }],
                //Will not allow player to resume on a time larger than 4 seconds
                onBeforeResume: function() {
                    if (this.getStatus().time >= 6)
                    return false;
                    return false;
                },
                //Deny seeking
                onBeforeSeek: function(target) {
                    return false;

                }
            }
            ,

            plugins: {
                    controls: {
                        url: "http://releases.flowplayer.org/swf/flowplayer.controls-3.2.15.swf",
                        autoHide: "yes"   // we don't want the controlbar to hide
                    },
                    pleaseLogIn: {url: "http://releases.flowplayer.org/swf/flowplayer.content-3.2.8.swf",
                        height: 360,
                        padding: 0,
                        width: 765,
                        top: 50,
                        opacity: 0,
                        border:0,
                        backgroundColor: 'transparent',
                        //Styling
                        borderRadius: 0,
                        style: {
                            'p': {
                                fontFamily: 'Lucida Grande,bitstream vera sans,trebuchet ms,verdana,arial',
                                textAlign: 'center',
                                fontSize: '34'
                            },
                            'a': {
                                fontSize: '36',
                                color: '#0000FF'
                            },
                            'a:hover': {
                                color: '#FF0000'
                            }
                        }

                        ,
                        html: '<a href="<?=GX_URL;?>/signup.php" target="_blank"><img src="<?=GX_URL;?>//assets/images/loginmember.png" alt="watch <?=$title;?> free movies online" title="watch <?=$title;?> free movies online" width="745" height="336" />',
                        opacity: 0
                     }
                }
            }

        );
    </script>
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <div class="center-block text-center">
        <div class="addthis_sharing_toolbox"></div>
    </div>
</div>
    </div><!-- /.row -->

</div><!-- /.container -->
<div class="infobox">
    <div class="container" itemscope itemtype="http://schema.org/Movie">
        <div class="col-sm-3" >
            <img src="<?=$p->poster;?>" class="img-responsive" >
        </div>
        <div class="col-sm-9">
            <div class="col-sm-12">
            <table class="table">
                <tr>
                    <td colspan="2" ><strong itemprop="name"><?=$param['title'];?></strong></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="table">
                        <tbody>
                            <tr>
                                <td><strong>Air Date :</strong> <time itemprop="datePublished" 
                                datetime="<?=$param['release_date'];?>"><?=date("d M, Y",strtotime($param['release_date']));?></time>  , 
                                <strong>Genres : </strong>
                                <?php
                                    $genre = '';
                                    foreach($param['genres'] as $k => $v){
                                        $genre .= "<span itemprop=\"genre\">{$v['name']}</span>, ";
                                    } 
                                    echo substr($genre, 0, -2);
                                ?>
                                <br />
                                <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                   <strong>Rating :</strong> 
                                   <span itemprop="ratingValue">
                                       <?=$p->rating;?>
                                   </span>/<span itemprop="bestRating">10</span>
                                   from 
                                   <span itemprop="ratingCount">
                                       <?=$p->votes;?>
                                   </span> Vote(s)
                                </span>
                                <br />
                                <strong>Writers :</strong>
                                    
                                    <?php
                                        $writers = '';
                                        foreach($param['crew'] as $k => $v){
                                            if($v['department'] == 'Writing'){
                                                $writers .= "
                                                <span itemprop=\"author\" itemscope itemtype=\"http://schema.org/Person\">
                                                    <span itemprop=\"name\">
                                                        {$v['name']}
                                                    </span>
                                                </span>, ";
                                            }
                                        } 
                                        echo substr($writers, 0, -2);
                                    ?>

                                <br />
                                <strong>Directors :</strong>
                                    <?php
                                        $director = '';
                                        foreach($param['crew'] as $k => $v){
                                            if($v['department'] == 'Directing'){
                                                $director .= "
                                                <span itemprop=\"director\" itemscope itemtype=\"http://schema.org/Person\">
                                                    <span itemprop=\"name\">
                                                        {$v['name']}
                                                    </span>
                                                </span>, ";
                                            }
                                        } 
                                        echo substr($director, 0, -2);
                                    ?>
                                    <meta itemprop="duration" content="T<?=$param['runtime']?>M" />
                                    <meta itemprop="image" content="<?=$p->backdrop?>" />
                                    <meta itemprop="thumbnailUrl" content="<?=$p->poster?>" />
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><strong>Storyline</strong></td>
                    <td itemprop="description"><?=$content;?></td>
                </tr>
                <tr>
                    <td><strong>Cast</strong></td>
                    <td >
                    <?php 
                    //print_r($param);
                    $cast = '';
                    foreach($param['cast'] as $k => $v){
                        $cast .= "
                        <span itemprop=\"actor\" itemscope itemtype=\"http://schema.org/Person\">
                            <span itemprop=\"name\">
                                {$v['name']}
                            </span>
                        </span>, ";
                    } 
                    echo substr($cast, 0, -2);
                    ?></td>
                </tr>
            </table>
                
            </div>
        </div>
    </div>
</div>

<?php
}else{
        echo "Error, Post not found.";
    }
    
?>