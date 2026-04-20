<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20150312
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data['sitetitle'] = _('Media');
Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));
Theme::admin('header', $data);
System::inc('media', $data);
Theme::admin('footer');

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */
