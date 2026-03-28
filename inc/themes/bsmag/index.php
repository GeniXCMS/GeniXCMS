<?php

if( isset($data['p_type']) && $data['p_type'] == 'index' ) {
    include "intro.php";
    include "featured.php";

}

echo "<div class=\"row g-5\">";
include "blogpost.php";
include "sidebar.php";
echo "</div>";

