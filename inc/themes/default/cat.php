<div class="col-sm-8 blog-main">
<?php
    if($data['num'] > 0){
        foreach ($data['posts'] as $p) {
            # code...
            echo "
            <div class=\"blog-post\">
                <h2 class=\"blog-post-title\"><a href=\"".Url::post($p->id)."\">$p->title</a></h2>
                <p class=\"blog-post-meta\">{$p->date} by <a href=\"#\">{$p->author}</a></p>
                ".Posts::content($p->content)."
            </div>
                ";
        }
        
    }else{
        echo "No Post to show";
    }
    echo $data['paging'];
?>
</div>
<?php Theme::theme('rightside', $data); ?>