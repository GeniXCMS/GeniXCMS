<?php
/**
 * Name: Code Highlight
 * Desc: Simple Code Highlight for GeniXCMS
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: Puguh Wijayanto
 * URI: http://www.metalgenix.com
 * License: MIT License
 * Icon: <i class="fa fa-code"></i>.
 */
function loadGXCodeHighLib($class_name)
{
    Mod::inc($class_name.'.lib', '', dirname(__FILE__).'/inc/');
}
spl_autoload_register('loadGXCodeHighLib');

new GxCodeHighlight();
