<?php
/**
 * BSMag Theme Functions
 *
 * Registers theme helpers and admin menu entries
 * for the BSMag Bootstrap Magazine theme.
 */

// Register "BSMag Options" sub-item under the Themes admin menu.
Hooks::attach('init', function () {
    AdminMenu::addChild('themes', [
        'label'  => _('BSMag Options'),
        'url'    => 'index.php?page=themes&view=options',
        'icon'   => 'bi bi-sliders',
        'access' => 0,
    ]);
});
