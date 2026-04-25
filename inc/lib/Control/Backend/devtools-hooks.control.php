<?php
/**
 * GeniXCMS - Developer Tools: Hook Inspector
 *
 * @since 2.4.0
 * @version 2.4.0
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

if (!defined('DEVELOPER_MODE') || !DEVELOPER_MODE) {
    Control::error('noaccess');
    return;
}

if (!User::access(0)) {
    Control::error('noaccess');
    return;
}

$data['sitetitle'] = _('Hook Inspector');

Theme::admin('header', $data);
System::inc('devtools-hooks', $data);
Theme::admin('footer');
