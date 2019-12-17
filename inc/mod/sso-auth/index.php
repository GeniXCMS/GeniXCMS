<?php
/**
 * Name: SSO Auth
 * Desc: Single Sign On Authentification
 * Version: 0.0.1
 * Build: 1.1.5
 * Developer: Puguh Wijayanto 
 * URI: http://www.metalgenix.com 
 * License: MIT License 
 * Icon: <i class="fa fa-key"></i>
 */

function loadSSOLib($class_name)
{
    Mod::inc($class_name.".lib",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadSSOLib');

new Sso();