<?php
define('GX_LIB', true);
require_once 'inc/lib/Control/Control.class.php';
require_once 'inc/lib/Db.class.php';
require_once 'inc/lib/Query.class.php';
require_once 'inc/lib/Typo.class.php';
require_once 'inc/lib/Options.class.php';

// Assuming config.php is present for DB connection
require_once 'config.php';

// Attempt to delete gneex_options
try {
    $db = new Db();
    $q = Query::table('options')->where('name', 'gneex_options')->delete();
    echo "gneex_options has been deleted from the database. Theme settings will reset to defaults.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
