<div class="col-sm-8 blog-main">
<?php
    if(isset($data['posts'][0]->title)){
        foreach ($data['posts'] as $p) {
            # code...
            echo "
            <div class=\"blog-post\">
                <h2 class=\"blog-post-title\"><a href=\"".Url::post($p->id)."\">$p->title</a></h2>
                <p class=\"blog-post-meta\">{$p->date} by <a href=\"#\">{$p->author}</a></p>
                ".Typo::Xclean($p->content)."
            </div>
                ";
        }
    }else{
        echo "Error, Post not found.";
    }
    
    
?>
</div>
<?php Theme::theme('rightside', $data); ?>