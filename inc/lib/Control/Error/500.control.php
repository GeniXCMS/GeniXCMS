<?php
header("HTTP/1.0 500 Internal Server Error");
if(Theme::exist('500')) {
    Theme::theme('500');
}else{
    echo "<center>
        <h1>Ooops!!</h1>
        <h2 style=\"font-size: 20em\">500</h2>
        <h3>Internal Server Error</h3>
        Back to <a href=\"".Options::get('siteurl')."\">".Options::get('sitename')."</a>
        </center>
        ";
    Site::footer();
}

