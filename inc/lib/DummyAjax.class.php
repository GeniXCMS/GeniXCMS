<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
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

class DummyAjax
{
    /**
     * Default action for dummy AJAX
     * Endpoint: index.php?ajax=dummy&token=TOKEN
     */
    public function index()
    {
        return Ajax::response([
            'status' => 'success',
            'message' => 'Dummy AJAX index called successfully'
        ]);
    }

    /**
     * Custom action for dummy AJAX
     * Endpoint: index.php?ajax=dummy&action=hello&token=TOKEN
     */
    public function hello()
    {
        $name = Typo::cleanX($_GET['name'] ?? 'World');
        return Ajax::response([
            'status' => 'success',
            'message' => "Hello, $name! This is a custom AJAX action."
        ]);
    }
}
