<?php
/**
 * Name: Infograph Post/Articles 
 * Desc: The infographic for Posts/Articles
 * Version: 0.0.1 
 * Build: 2.0.0-alpha 
 * Developer: GeniXCMS
 * URI: http://genixcms.web.id/ 
 * License: MIT License 
 * Icon: bi bi-easel2
 */

function loadInfographLib($class_name)
{
    Mod::inc($class_name.".lib",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadInfographLib');

new Infograph();
