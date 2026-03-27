<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.0
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(0)) {
    $data['sitetitle'] = _('ACL Manager');
    
    if (isset($_POST['save_acl'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!Token::validate($token)) {
            $data['alertDanger'][] = _("Invalid token. Please refresh and try again.");
        } else {
            $perms = $_POST['perm'] ?? [];
            // Clear existing for these groups to be safe? 
            // Or just update. Our Acl::set handles it.
            
            foreach ($perms as $group_id => $p_list) {
                foreach ($p_list as $perm_key => $status) {
                    Acl::set($group_id, $perm_key, $status);
                }
            }
            $data['alertSuccess'][] = _("Access Control List updated successfully.");
            Token::remove($token);
        }
    }

    $data['groups'] = User::$group;
    $data['permissions'] = Acl::getAllPermissions();

    Theme::admin('header', $data);
    System::inc('permissions', $data);
    Theme::admin('footer', $data);
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
