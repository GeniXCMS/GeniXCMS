<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150312
* @version 0.0.7-alpha.1
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/



$data['sitetitle'] = "Media";
Hooks::attach('admin_footer_action', array('Files','elfinderLib'));
Theme::admin('header', $data);
System::inc('media',$data);
Theme::admin('footer');


/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */