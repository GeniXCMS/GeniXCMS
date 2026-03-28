<?php
/**
 * Name: Books Library 
 * Desc: Books Management for Library
 * Version: 0.0.2 
 * Build: 2.0.0 
 * Developer: Puguh Wijayanto 
 * URI: http://www.metalgenix.com 
 * License: MIT License 
 * Icon: fa fa-cogs
 */

function loadBooksLib($class_name)
{
    Mod::inc($class_name . ".lib", '', dirname(__FILE__) . "/inc/");
}
spl_autoload_register('loadBooksLib');

new Mods();