<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150219
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
header("HTTP/1.0 500 Internal Server Error");
if(Theme::exist('500')) {
    Theme::theme('500');
}else{
    echo "<center>
        <h1>Ooops!!</h1>
        <h2 style=\"font-size: 20em\">500</h2>
        <h3>Internal Server Error</h3>
        Back to <a href=\"".Options::get('siteurl')."\">".Options::get('sitename')."</a>
        </center>
        ";
    Site::footer();
}

