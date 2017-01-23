<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140805
 *
 * @version 1.0.1
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2017 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$route = Router::scrap($param);
//echo $route['mod'];
$data['mod'] = (SMART_URL) ? $route['mod'] : Typo::cleanX($_GET['mod']);
$data['p_type'] = 'mod';
//echo $data['mod'];
$data['sitetitle'] = Mod::getTitle($data['mod']);

if (Hooks::exist($data['mod'], 'mod_control')) { // check if mod exist at hooks mod_control
    Theme::theme('header', $data);
    Theme::theme('mod', $data);
    Theme::footer($data);
    exit();
} else {
    Control::error('404');
    exit();
}

/* End of file mod.control.php */
/* Location: ./inc/lib/Control/Frontend/mod.control.php */
