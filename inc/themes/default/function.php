<?php

function loadMdoClass($class_name)
{
    Mod::inc($class_name.".class", '', dirname(__FILE__)."/inc/");
}
spl_autoload_register('loadMdoClass');

new MdoTheme();

// Register "Default Theme Options" sub-item under the Themes admin menu.
Hooks::attach('init', function () {
    AdminMenu::addChild('themes', [
        'label'  => _('Default Theme Options'),
        'url'    => 'index.php?page=themes&view=options',
        'icon'   => 'bi bi-sliders',
        'access' => 0,
    ]);
});
