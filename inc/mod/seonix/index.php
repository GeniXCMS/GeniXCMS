<?php
/**
 * Name: SEONIX 
 * Desc: Official SEO from GeniXCMS
 * Version: 0.0.1 
 * Build: 2.0.0-alpha 
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/ 
 * License: MIT License 
 * Icon: fa fa-cogs
 */

function loadSeonixLib($class_name)
{
    Mod::inc($class_name.".lib",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadSeonixLib');

new SeoNix();
