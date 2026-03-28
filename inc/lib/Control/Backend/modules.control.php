<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 *
 * @version 2.0.0
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
        //print_r($mod);
            $zip = new ZipArchive();
            if ($zip->open($mod['filepath']) === true) {
                $dir = explode('/', $zip->statIndex(0)['name']);
                // print_r($dir);
                if (count($dir) == 1) {
                    $zip->close();
                    @unlink($mod['filepath']);
                    $data['alertDanger'][] = _('Failed to Install your module');
                } else {
                    $zip->extractTo(GX_MOD);
                    $entry = [];
                    for($i = 0; $i < $zip->numFiles; $i++) {
                        $entry[] = $zip->getNameIndex($i);
                    }
                    $zip->close();
                    foreach ($entry as $key => $value) {
                        // echo $value;
                        $handle = fopen(GX_MOD.$value, 'r');
                        $file = fread($handle, filesize(GX_MOD.$value));
                        fclose($handle);
                        preg_match('/(.*)(phpinfo|system|php_uname|chmod|fopen|flclose|readfile|base64_decode|passthru)(.*)/Us', $file, $matches);
                        if (count($matches) > 0) {
                            @unlink(GX_MOD.$value);
                            Files::delTree(GX_MOD.$dir[0]);
                            @unlink($mod['filepath']);
                            $data['alertDanger'][] = _('Failed to Install your module');
                        } else {
                            $data['alertSuccess'][] = _("Module Installed Successfully.");
                        }
                    }
                }

                Hooks::run('module_install_action', $mod);
                $data['alertSuccess'][] = _("Module Installed Successfully.");
            } else {
                $data['alertDanger'][] = _("Cannot extract files.");
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
