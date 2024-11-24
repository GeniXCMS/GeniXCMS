<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 *
 * @version 1.1.12
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

header('HTTP/1.0 400 Bad Request');
if (Theme::exist('400')) {
    Theme::theme('400');
} else {
    Site::meta();
    echo '<center>
        <h1>Ooops!!</h1>
        <h2 style="font-size: 20em">400</h2>
        <h3>Bad Request</h3>
        Back to <a href="'.Options::v('siteurl').'">'.Options::v('sitename').'</a>
        </center>
        ';
    Site::footer();
}
