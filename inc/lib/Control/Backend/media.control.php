<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 *
 * @version 1.1.3
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genix.id
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2017 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data['sitetitle'] = 'Media';
Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));
Theme::admin('header', $data);
System::inc('media', $data);
Theme::admin('footer');

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */
