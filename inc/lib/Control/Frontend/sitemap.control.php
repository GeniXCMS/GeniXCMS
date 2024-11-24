<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141007
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
System::gZip();
$data = Router::scrap($param);
$data['sitemap'] = (SMART_URL) ? $data['sitemap'] : Typo::cleanX($_GET['sitemap']);
$map = Sitemap::$_map;

if (isset($data['sitemap']) && $data['sitemap'] != '') {
    # code...
    $cat = Categories::id($data['sitemap']);
    $type = Categories::type($cat);
    Sitemap::create($type, 3000, $map[$type]['url'], $map[$type]['class'], $cat);
}else{
    Sitemap::createIndex();
}
System::Zipped();
/* End of file sitemap.control.php */
/* Location: ./inc/lib/Control/Frontend/sitemap.control.php */
