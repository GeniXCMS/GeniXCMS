<?php

function loadMdoClass($class_name)
{
    Mod::inc($class_name.".class",'',dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadMdoClass');

new MdoTheme();
