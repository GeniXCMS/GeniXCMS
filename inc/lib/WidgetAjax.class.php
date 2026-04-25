<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class WidgetAjax
{
    /**
     * Retrieves the parameter form for a specific widget type.
     * Used via AJAX to dynamically load widget configurations.
     */
    public function get_params()
    {
        error_log("DEBUG: WidgetAjax::get_params called for type: " . ($_GET['type'] ?? 'none'));
        Ajax::init();
        if (!Ajax::auth()) {
            Ajax::error(403, "Unauthorized access.");
        }

        $type = $_GET['type'] ?? '';
        $id = Typo::int($_GET['id'] ?? 0);

        // Mock state for Params::build()
        $_GET['page'] = 'widgets';
        if ($id) $_GET['id'] = $id;

        // Force Widget.class.php to autoload so Widget::setup() runs and
        // registers all widget type params (navigation, recent_posts, etc.)
        // into the Params system BEFORE we call renderWidget().
        Widget::getLocations();

        // Fetch dynamic parameter fields from Params system
        $html = Params::renderWidget($type);

        Ajax::response([
            'status' => 'success',
            'type' => $type,
            'html' => $html
        ]);
    }
}
