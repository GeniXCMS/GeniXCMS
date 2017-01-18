<?php
/**
 * Name: Contact Form
 * Desc: This is the Description of the Modules.
 * Version: 0.0.1
 * Build: 1.0.0 
 * Developer: Puguh Wijayanto 
 * URI: http://www.metalgenix.com 
 * License: MIT License 
 * Icon: <i class="fa fa-cogs"></i>
 */

function loadContactFormLib($class_name)
{
    Mod::inc($class_name.".lib",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadContactFormLib');

new Contact();
