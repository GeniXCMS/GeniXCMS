<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

    $data['sitetitle'] = DASHBOARD;
    Theme::admin('header', $data);
    System::inc('dashboard', $data);
    //Mod::Options('moviedb');
    Theme::admin('footer');

/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
