<?php
    $gneex = Gneex::$opt;
    $post = Gneex::getPost($gneex['fullwidth_page']);
    echo(Typo::Xclean($post));
?>
