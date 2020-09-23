<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
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

if (User::access(0)) {
    $data['sitetitle'] = MODULES;
    if (isset($GLOBALS['alertDanger'])) {
        $data['alertDanger'] = $GLOBALS['alertDanger'];
    }
    if (isset($GLOBALS['alertSuccess'])) {
        $data['alertSuccess'][] = $GLOBALS['alertSuccess'];
    }

    if (isset($_POST['upload'])) {
        if (!Token::validate($_POST['token'])) {
            $alertDanger[] = TOKEN_NOT_EXIST;
        }
        if (!isset($_FILES['module']['name']) || $_FILES['module']['name'] == '') {
            $alertDanger[] = NOFILE_UPLOADED;
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
                    $data['alertDanger'][] = 'Failed to Install your module';
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
                            $data['alertDanger'][] = 'Failed to Install your module';
                        } else {
                            $data['alertSuccess'][] = MSG_MOD_INSTALLED;
                        }
                    }
                }

                Hooks::run('module_install_action', $mod);
                $data['alertSuccess'][] = MSG_MOD_INSTALLED;
            } else {
                $data['alertDanger'][] = MSG_MOD_CANT_EXTRACT;
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
