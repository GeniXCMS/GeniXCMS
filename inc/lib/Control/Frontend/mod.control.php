<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140805
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
System::gZip(true);

$route = Router::scrap($param);
//echo $route['mod'];
$data['mod'] = (SMART_URL) ? $route['mod'] : Typo::cleanX($_GET['mod']);
$data['p_type'] = 'mod';
//echo $data['mod'];
$data['sitetitle'] = Mod::getTitle($data['mod']);

if (Hooks::exist($data['mod'], 'mod_control')) { // check if mod exist at hooks mod_control
    Cache::start();
    Theme::theme('header', $data);
    Theme::theme('mod', $data);
    Theme::footer($data);
    Cache::end();
    exit();
} else {
    Control::error('404');
    exit();
}

System::Zipped();
/* End of file mod.control.php */
/* Location: ./inc/lib/Control/Frontend/mod.control.php */
