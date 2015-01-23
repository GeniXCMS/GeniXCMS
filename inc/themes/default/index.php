<div class="col-sm-8 blog-main">
<?php
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
    if(isset($_GET['paging'])){
        $paging = $_GET['paging'];
    }else{
        $paging = 1;
    }
    $paging = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => '`type` = \'post\'',
                    'max' => '10',
                    'url' => '',
                    'type' => 'pager'
                );
    echo Paging::create($paging, true);
?>
</div>
<?php Theme::theme('rightside', $data); ?>