<?php
header("HTTP/1.0 403 Forbidden");
if(Theme::exist('403')) {
    Theme::theme('403');
}else{
    echo "<center>
        <h1>Ooops!!</h1>
        <h2 style=\"font-size: 20em\">403</h2>
        <h3>Forbidden</h3>
        Back to <a href=\"".Options::get('siteurl')."\">".Options::get('sitename')."</a>
        </center>
        ";
    Site::footer();
}

