<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20150312
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(0)) {
    $data['sitetitle'] = _("Modules");
    if (isset($GLOBALS['alertDanger'])) {
        $data['alertDanger'] = $GLOBALS['alertDanger'];
    }
    if (isset($GLOBALS['alertSuccess'])) {
        $data['alertSuccess'][] = $GLOBALS['alertSuccess'];
    }

    if (isset($_POST['upload'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) && !Token::validate($token, true)) {
            // VALIDATE ALL
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (!isset($_FILES['module']['name']) || $_FILES['module']['name'] == '') {
            $alertDanger[] = _("No Files Uploaded.");
        }

        if (!isset($alertDanger)) {
            //Mod::activate($_GET['modules']);
            $path = '/inc/mod/';
            $allowed = array('zip');
            $mod = Upload::go('module', $path, $allowed);

            // SECURITY SCAN (Pre-Extraction)
            $scanResult = Security::scanZip($mod['filepath']);

            if ($scanResult['status'] === true) {
                $zip = new ZipArchive();
                if ($zip->open($mod['filepath']) === true) {
                    $dir = explode('/', $zip->statIndex(0)['name']);
                    // print_r($dir);
                    if (count($dir) == 1) {
                        $zip->close();
                        @unlink($mod['filepath']);
                        $data['alertDanger'][] = _('Failed to Install your module: Invalid ZIP structure.');
                    } else {
                        $zip->extractTo(GX_MOD);
                        $zip->close();

                        Hooks::run('module_install_action', $mod);
                        $data['alertSuccess'][] = _("Module Installed Successfully.");
                    }
                } else {
                    $data['alertDanger'][] = _("Cannot extract files.");
                }
            } else {
                // SCAN FAILED - POTENTIAL MALWARE
                $data['alertDanger'] = array_merge($data['alertDanger'] ?? [], $scanResult['errors']);
                $data['alertDanger'][] = _('Failed to Install your module: Security check failed.');
            }
            unlink($mod['filepath']);
        } else {
            $data['alertDanger'] = $alertDanger;
        }
        if (isset($_POST['token'])) {
            Token::remove(Typo::cleanX($_POST['token']));
        }
    }
    $data['mods'] = Mod::modList();
    Theme::admin('header', $data);
    System::inc('modules', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */
