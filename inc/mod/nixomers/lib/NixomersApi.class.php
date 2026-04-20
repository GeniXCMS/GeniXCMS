<?php
/**
 * Nixomers API Controller (Modular Version)
 * Acts as a generic dispatcher for Nixomers RESTful requests.
 * Gateway-specific logic (like Pakasir) is handled via Hooks.
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixomersApi
{
    public $is_public = true;

    /**
     * GET Dispatcher
     */
    public function index($identifier = null)
    {
        $action = $_GET['action'] ?? '';

        // Default: Get Order by ID
        if ($identifier && empty($action)) {
             $order = Query::table('nix_orders')->where('order_id', $identifier)->first();
             return ($order) ? Api::success($order) : Api::error(404, 'Order not found');
        }

        if (empty($action)) {
            return Api::error(400, 'Missing action parameter');
        }

        // Dispatch call to modules
        // Hooks return should be the result of Api::success or Api::error
        $response = Hooks::run('nix_api_' . $action, $_GET);
        
        // Hooks::run returns a string (concatenated results). 
        // We need to handle this carefully if multiple modules attach to the same action.
        // For REST API, usually only one module handles one specific action.
        if (!empty($response)) {
            return $response;
        }

        return Api::error(404, "Action '{$action}' not found or handled");
    }

    /**
     * POST Dispatcher
     */
    public function submit()
    {
        $action = $_GET['action'] ?? '';
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        if (empty($action)) {
            return Api::error(400, 'Missing action parameter');
        }

        $response = Hooks::run('nix_api_' . $action, $data);
        
        if (!empty($response)) {
            return $response;
        }

        return Api::error(404, "Action '{$action}' not found or handled");
    }
}
