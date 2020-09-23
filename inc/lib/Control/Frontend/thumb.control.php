<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20160902
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
System::gZip();

$data = Router::scrap($param);
// print_r($data);
if (isset($data['thumb']) && $data['thumb'] != '' ) {
    $thumb = isset($data['thumb']) ? $data['thumb']: '';
    $type = isset($data['type']) ? $data['type'] : '';
    $size = isset($data['size']) ? $data['size'] : '';
    $align = isset($data['align']) ? $data['align'] : '';
} elseif( isset($_GET['thumb']) && $_GET['thumb'] != '') {
    $thumb = isset($_GET['thumb'])  ? $_GET['thumb']: '';
    $type = isset($_GET['type'])  ? $_GET['type'] :'';
    $size = isset($_GET['size'])  ? $_GET['size'] : '';
    $align = isset($_GET['align'])  ? $_GET['align'] : '';
} else {
    $thumb = '';
    $type = '';
    $size = '';
    $align = '';
}
Image::thumbFly($thumb, $type, $size, $align);

System::Zipped();