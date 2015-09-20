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
            <hr />
            <div class=\"col-sm-12\">
                <div class=\"row\">
                <h3>Comments</h3>
                <div class=\"fb-comments\" data-href=\"http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\" data-width=\"100%\" data-numposts=\"5\" data-colorscheme=\"light\"></div>
                </div>
            </div>
                ";
        }
    }else{
        //echo "Error, Post not found."; 
        Control::error('404');
    }
    
    
?>
</div>
<?php Theme::theme('rightside', $data); ?>