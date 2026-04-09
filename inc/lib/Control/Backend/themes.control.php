<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(0)) {
    $data = [];
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
                $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
            }
            if (!isset($_FILES['theme']['name']) || $_FILES['theme']['name'] == '') {
                $alertDanger[] = _("No Files Uploaded.");
            }

            if (!isset($alertDanger)) {
                //Mod::activate($_GET['themes']);
                $path = '/inc/themes/';
                $allowed = array('zip');
                $theme = Upload::go('theme', $path, $allowed);

                // SECURITY SCAN (Pre-Extraction)
                $scanResult = Security::scanZip($theme['filepath']);

                if ($scanResult['status'] === true) {
                    $zip = new ZipArchive();
                    if ($zip->open($theme['filepath']) === true) {
                        $dir = explode('/', $zip->statIndex(0)['name']);
                        // print_r($dir);
                        if (count($dir) == 1) {
                            $zip->close();
                            @unlink($theme['filepath']);
                            $data['alertDanger'][] = _('Failed to Install your theme: Invalid ZIP structure.');
                        } else {
                            $zip->extractTo(GX_THEME);
                            $zip->close();

                            Hooks::run('theme_install_action', $theme);
                            $data['alertSuccess'][] = _("Theme Installed Successfully.");
                        }
                    } else {
                        $data['alertDanger'][] = _("Cannot extract files.");
                    }
                } else {
                    // SCAN FAILED - POTENTIAL MALWARE
                    $data['alertDanger'] = array_merge($data['alertDanger'] ?? [], $scanResult['errors']);
                    $data['alertDanger'][] = _('Failed to Install your theme: Security check failed.');
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
                    $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                }

                if (!isset($alertDanger)) {
                    Theme::activate(Typo::cleanX($_GET['themes']));
                    Hooks::run('theme_activated_action', Typo::cleanX($_GET['themes']));
                    $data['alertSuccess'][] = _("Themes activated.");
                } else {
                    $data['alertDanger'] = $alertDanger;
                }
            } elseif ($_GET['act'] == 'remove') {
                $token = Typo::cleanX($_GET['token']);
                if (!Token::validate($_GET['token'])) {
                    $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                }
                if (Theme::isActive(Typo::cleanX($_GET['themes']))) {
                    $alertDanger[] = _("Theme is Active. Please deactivate first.");
                }
                if (!isset($alertDanger)) {
                    if (Files::delTree(GX_THEME . '/' . $_GET['themes'])) {
                        $data['alertSuccess'][] = _("Themes removed.");
                    } else {
                        $data['alertDanger'][] = _("Theme Cannot removed. Please check if You had permission to remove the files.");
                    }
                } else {
                    $data['alertDanger'] = $alertDanger;
                }
            }
        }


        $data['sitetitle'] = _("Themes");
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
