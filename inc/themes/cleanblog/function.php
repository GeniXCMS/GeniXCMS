<?php
/**
 * Clean Blog Latte Theme Functions
 */

class CleanBlogLatte
{
    public function __construct() {
        // Initialization if needed
    }
}

new CleanBlogLatte();

// Register "Clean Blog Latte Options" sub-item under the Themes admin menu.
Hooks::attach('init', function () {
    AdminMenu::addChild('themes', [
        'label'  => _('Clean Blog Options'),
        'url'    => 'index.php?page=themes&view=options',
        'icon'   => 'bi bi-sliders',
        'access' => 0,
    ]);
});
