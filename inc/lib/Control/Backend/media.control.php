<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 *
 * @version 1.1.12
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data['sitetitle'] = 'Media';
Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));
Theme::admin('header', $data);
System::inc('media', $data);
Theme::admin('footer');

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */
