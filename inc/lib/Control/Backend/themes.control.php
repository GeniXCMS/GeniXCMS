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
    if (isset($_GET['view']) && $_GET['view'] == 'options') {
        $theme = Options::v('themes');
        $data['sitetitle'] = Theme::name($theme);
        Theme::admin('header', $data);
        Theme::options($theme);
        Theme::admin('footer');
    } else {
        if (isset($_POST['upload'])) {
            $token = Typo::cleanX($_POST['token']);
            if (!Token::validate($token)) {
                $alertDanger[] = TOKEN_NOT_EXIST;
            }
            if (!isset($_FILES['theme']['name']) || $_FILES['theme']['name'] == '') {
                $alertDanger[] = NOFILE_UPLOADED;
            }

            if (!isset($alertDanger)) {
                //Mod::activate($_GET['themes']);
                $path = '/inc/themes/';
                $allowed = array('zip');
                $theme = Upload::go('theme', $path, $allowed);
                //print_r($theme);
                $zip = new ZipArchive();
                if ($zip->open($theme['filepath']) === true) {
                    $dir = explode('/', $zip->statIndex(0)['name']);
                    // print_r($dir);
                    if (count($dir) == 1) {
                        $zip->close();
                        @unlink($mod['filepath']);
                        $data['alertDanger'][] = 'Failed to Install your theme';
                    } else {
                        $zip->extractTo(GX_THEME);
                        $entry = [];
                        for($i = 0; $i < $zip->numFiles; $i++) {
                            $entry[] = $zip->getNameIndex($i);
                        }
                        $zip->close();
                        foreach ($entry as $key => $value) {
                            // echo $value;
                            $handle = fopen(GX_THEME.$value, 'r');
                            $file = fread($handle, filesize(GX_THEME.$value));
                            fclose($handle);
                            preg_match('/(.*)(phpinfo|system|php_uname|chmod|fopen|flclose|readfile|base64_decode|passthru)(.*)/Us', $file, $matches);
                            if (count($matches) > 0) {
                                @unlink(GX_THEME.$value);
                                Files::delTree(GX_THEME.$dir[0]);
                                @unlink($mod['filepath']);
                                $data['alertDanger'][] = 'Failed to Install your theme';
                            } else {
                                $data['alertSuccess'][] = MSG_THEME_INSTALLED;
                            }
                        }
                    }

                    Hooks::run('theme_install_action', $theme);
                    
                } else {
                    $data['alertDanger'][] = MSG_THEME_CANT_EXTRACT;
                }
                unlink($theme['filepath']);
            } else {
                $data['alertDanger'] = $alertDanger;
            }
            if (isset($_POST['token'])) {
                Token::remove($token);
            }
        }

        if (isset($_GET['act'])) {
            if ($_GET['act'] == 'activate') {
                $token = Typo::cleanX($_GET['token']);
                if (!Token::validate($token)) {
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }

                if (!isset($alertDanger)) {
                    Theme::activate(Typo::cleanX($_GET['themes']));
                    $data['alertSuccess'][] = THEME_ACTIVATED;
                } else {
                    $data['alertDanger'] = $alertDanger;
                }
            } elseif ($_GET['act'] == 'remove') {
                $token = Typo::cleanX($_GET['token']);
                if (!Token::validate($_GET['token'])) {
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }
                if (Theme::isActive(Typo::cleanX($_GET['themes']))) {
                    $alertDanger[] = MSG_THEME_IS_ACTIVE;
                }
                if (!isset($alertDanger)) {
                    if (Files::delTree(GX_THEME.'/'.$_GET['themes'])) {
                        $data['alertSuccess'][] = THEME_REMOVED;
                    } else {
                        $data['alertDanger'][] = MSG_THEME_NOT_REMOVED;
                    }
                } else {
                    $data['alertDanger'] = $alertDanger;
                }
            }
        }

        $data['sitetitle'] = THEMES;
        $data['themes'] = Theme::thmList();
        Theme::admin('header', $data);
        System::inc('themes', $data);
        Theme::admin('footer');
    }
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */
