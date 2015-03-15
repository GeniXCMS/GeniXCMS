<div class="col-sm-8 blog-main">
<?php
    foreach ($data['posts'] as $p) {
        # code...
        echo "
        <div class=\"blog-post\">
            <h2 class=\"blog-post-title\">$p->title</h2>
           
           ".Typo::Xclean($p->content)."
        </div>
            ";
    }
?>
</div>
<?php Theme::theme('rightside', $data); ?>