<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.0
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Api
{
    private static $_response_code = 200;
    private static $_headers_sent = false;

    public static function init()
    {
        // Set standard API headers
        if (!self::$_headers_sent) {
            header('Content-Type: application/json; charset=UTF-8');
            self::$_headers_sent = true;
        }
    }

    public static function dispatch($resource, $identifier = null, $action = null)
    {
        self::init();
        
        // Simple API Authentication check
        if (!self::auth()) {
            return self::error(401, 'Unauthorized access [API Key Missing or Invalid]');
        }

        $resourceClass = ucfirst($resource) . 'Api'; // e.g. PostsApi
        if (class_exists($resourceClass)) {
            $api = new $resourceClass();
            $method = $_SERVER['REQUEST_METHOD'];
            
            try {
                switch ($method) {
                    case 'GET':
                        if ($identifier) {
                            return $api->index($identifier);
                        }
                        return $api->index();
                    case 'POST':
                        return $api->submit();
                    case 'PUT':
                    case 'PATCH':
                        if (!$identifier) return self::error(400, 'Missing identifier for update');
                        return $api->update($identifier);
                    case 'DELETE':
                        if (!$identifier) return self::error(400, 'Missing identifier for deletion');
                        return $api->delete($identifier);
                    default:
                        return self::error(405, 'Method not allowed');
                }
            } catch (Exception $e) {
                return self::error(500, 'Internal Server Error: ' . $e->getMessage());
            }
        } else {
            return self::error(404, "Resource '$resource' not found");
        }
    }

    public static function auth() {
        // Look for GX-API-KEY in header or query string
        $key = $_SERVER['HTTP_GX_API_KEY'] ?? $_GET['api_key'] ?? null;
        $saved_key = Options::v('api_key');
        
        if ($key && $saved_key && $key === $saved_key) {
            return true;
        }
        
        // Fallback: Check if user is logged in (for internal dash use)
        if (Session::val('logged_in')) {
            return true;
        }

        return false;
    }

    public static function response($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error($code, $message)
    {
        return self::response([
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ], $code);
    }

    public static function success($data, $message = 'Operation successful')
    {
        return self::response([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 200);
    }
}
