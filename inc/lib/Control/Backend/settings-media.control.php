<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20160313
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
    $data['sitetitle'] = _('Permalink');

    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) && !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }

        if (!isset($alertDanger)) {
            $vars = array();
            $flip = array_flip($_POST);
        // print_r($_POST);
            $sql = "SELECT * FROM `options` WHERE `value` = 'on'";
            $q = Db::result($sql);
            $input = array('media_use_watermark', 'media_autogenerate_webp', 'media_autoresize_image');

            foreach ($q as $ob) {
                if (in_array($ob->name, $input)) {
                    if (isset($flip[$ob->name])) {
                        $vars[$ob->name] = 'on';
                    } else {
                        $vars[$ob->name] = 'off';
                    }

                    $files = glob(GX_PATH.'/assets/cache/thumbs/thumb*'); // get all file names
                    foreach($files as $file){ // iterate files
                        if(is_file($file)) {
                            @unlink($file); // delete file
                        }
                    }
                    $file = @fopen(GX_PATH."/assets/cache/thumbs/index.html", 'w');
                    fclose($file);
                }
            }

            unset($_POST['token']);
            unset($_POST['change']);
        // print_r($vars);
            foreach ($_POST as $key => $val) {
                $vars[$key] = Typo::cleanX($val);
            }
        // print_r($vars);
            if( count($vars) > 0 )
                Options::update($vars);
                $data['alertSuccess'][] = _("Settings Updated");
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

    $data['media_use_watermark'] = Options::v('media_use_watermark');
    $data['media_watermark_image'] = Options::v('media_watermark_image');
    $data['media_watermark_position'] = Options::v('media_watermark_position');
    $data['media_autogenerate_webp'] = Options::v('media_autogenerate_webp');
    $data['media_autoresize_image'] = Options::v('media_autoresize_image');
    $data['media_autoresize_width'] = Options::v('media_autoresize_width');
    $data['media_watermark_opacity'] = Options::v('media_watermark_opacity');
    $data['media_watermark_position'] = Options::v('media_watermark_position');

    Theme::admin('header', $data);
    System::inc('settings-media', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
