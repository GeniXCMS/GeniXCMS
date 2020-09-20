<?php
/**
 * Name: GeniXCMS Sample MOD 
 * Desc: This is the Description of the Modules.
 * Version: 0.0.2 
 * Build: 1.0.0 
 * Developer: Puguh Wijayanto 
 * URI: http://www.metalgenix.com 
 * License: MIT License 
 * Icon: <i class="fa fa-cogs"></i>
 */

function loadModLib($class_name)
{
    Mod::inc($class_name.".lib",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadModLib');

new Mods();