<?php
header("HTTP/1.0 400 Bad Request");
if(Theme::exist('400')) {
    Theme::theme('400');
}else{
    echo "<center>
        <h1>Ooops!!</h1>
        <h2 style=\"font-size: 20em\">400</h2>
        <h3>Bad Request</h3>
        Back to <a href=\"".Options::get('siteurl')."\">".Options::get('sitename')."</a>
        </center>
        ";
    Site::footer();
}

