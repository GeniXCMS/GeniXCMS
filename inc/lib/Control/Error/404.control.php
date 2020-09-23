<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
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

header('HTTP/1.0 404 Not Found');
if (Theme::exist('404')) {
    Theme::theme('404');
} else {
    Site::meta();

    echo '<center>
        <h1>Ooops!!</h1>
        <h2 style="font-size: 20em">404</h2>
        <h3>Page Not Found</h3>
        Back to <a href="'.Options::v('siteurl').'">'.Options::v('sitename').'</a>
        </center>
        ';
    Site::footer();
}
