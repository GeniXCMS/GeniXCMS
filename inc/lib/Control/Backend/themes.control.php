<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(0)) {
    if (isset($_GET['view']) && $_GET['view'] == 'options') {
        $data['sitetitle'] = THEMES;
        Theme::admin('header', $data);
        Theme::options(Options::v('themes'));
        Theme::admin('footer');
    } else {
        if (isset($_POST['upload'])) {
            if (!Token::isExist($_POST['token'])) {
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
                    $zip->extractTo(GX_THEME);
                    $zip->close();
                    Hooks::run('theme_install_action', $theme);
                    $data['alertSuccess'][] = MSG_THEME_INSTALLED;
                } else {
                    $data['alertDanger'][] = MSG_THEME_CANT_EXTRACT;
                }
                unlink($theme['filepath']);
            } else {
                $data['alertDanger'] = $alertDanger;
            }
            if (isset($_POST['token'])) {
                Token::remove($_POST['token']);
            }
        }

        if (isset($_GET['act'])) {
            if ($_GET['act'] == 'activate') {
                if (!Token::isExist($_GET['token'])) {
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }

                if (!isset($alertDanger)) {
                    Theme::activate($_GET['themes']);
                    $data['alertSuccess'][] = THEME_ACTIVATED;
                } else {
                    $data['alertDanger'] = $alertDanger;
                }
            } elseif ($_GET['act'] == 'remove') {
                if (!Token::isExist($_GET['token'])) {
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }
                if (Theme::isActive($_GET['themes'])) {
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
