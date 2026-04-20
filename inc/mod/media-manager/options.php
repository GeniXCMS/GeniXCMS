<?php
/**
 * Media Manager Options Router
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$sel = Typo::cleanX($_GET['sel'] ?? 'manager');
$optionsDir = __DIR__ . '/options';

switch ($sel) {
    case 'manager':
    default:
        echo Mod::inc('manager', $data, $optionsDir);
        break;
}
