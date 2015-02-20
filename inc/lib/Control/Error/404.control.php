<?php
header("HTTP/1.0 404 Not Found");
if(Theme::exist('404')) {
    Theme::theme('404');
}else{
    echo "<center>
        <h1>Ooops!!</h1>
        <h2 style=\"font-size: 20em\">404</h2>
        <h3>Page Not Found</h3>
        Back to <a href=\"".Options::get('siteurl')."\">".Options::get('sitename')."</a>
        </center>
        ";
    Site::footer();
}

