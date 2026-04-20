<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Api
{
    private static $_response_code = 200;
    private static $_headers_sent = false;

    /**
     * Initializes the API by sending standard JSON headers.
     */
    public static function init()
    {
        // Set standard API headers
        if (!self::$_headers_sent) {
            header('Content-Type: application/json; charset=UTF-8');
            self::$_headers_sent = true;
        }
    }

    /**
     * Dispatches the API request to the appropriate resource controller.
     * Handles HTTP methods (GET, POST, PUT, DELETE), authentication, and rate limiting.
     *
     * @param string      $resource   The API resource (e.g., 'posts').
     * @param string|null $identifier Specific resource ID or slug.
     * @param string|null $action     Specific action name (optional).
     */
    public static function dispatch($resource, $identifier = null, $action = null)
    {
        self::init();

        // API Rate Limiting check
        if (!self::rateLimit()) {
            return self::error(429, 'Too many requests [Rate Limit Exceeded]');
        }

        // Go Backend: Programmatically and dynamically extensible list
        $coreGo = ['posts', 'categories', 'search', 'version', 'stats', 'tags'];
        $dbGo = explode(',', Options::v('go_service_whitelist') ?? '');
        $goSupported = array_unique(array_merge($coreGo, array_map('trim', $dbGo)));
        $goSupported = Hooks::filter('go_api_supported_resources', $goSupported);
        $backend = Options::v('api_backend') ?: 'php';
        if ($backend === 'go' && in_array($resource, $goSupported)) {
            return self::proxyToGo($resource, $identifier, $action);
        }

        $resourceClass = ucfirst($resource) . 'Api'; // e.g. PostsApi
        if (class_exists($resourceClass)) {
            $api = new $resourceClass();
            $method = $_SERVER['REQUEST_METHOD'];

            // Check if the API Endpoint flags itself as public
            $is_public = (property_exists($api, 'is_public') && $api->is_public === true);

            // Simple API Authentication check
            if (!$is_public && !self::auth()) {
                return self::error(401, 'Unauthorized access [API Key Missing or Invalid]');
            }

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
                        if (!$identifier)
                            return self::error(400, 'Missing identifier for update');
                        return $api->update($identifier);
                    case 'DELETE':
                        if (!$identifier)
                            return self::error(400, 'Missing identifier for deletion');
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

    /**
     * Transparently proxy the API request to the Go service.
     * The response format is identical to the PHP backend.
     * If Go is unreachable and fallback is enabled, switches back to PHP automatically.
     */
    private static function proxyToGo($resource, $identifier = null, $action = null)
    {
        $goUrl  = rtrim(Options::v('go_service_url') ?: 'http://localhost:8080', '/');
        $secret = Options::v('go_service_secret') ?: '';

        // Build path identical to GeniXCMS REST structure
        $path = '/api/' . ltrim($resource, '/');
        if ($identifier) $path .= '/' . $identifier;
        if ($action)     $path .= '/' . $action;

        // Forward original query string (except internal params)
        $params = $_GET;
        unset($params['api'], $params['resource'], $params['id']);
        $qs = http_build_query($params);
        if ($qs) $path .= '?' . $qs;

        $method  = $_SERVER['REQUEST_METHOD'];
        $body    = ($method !== 'GET') ? file_get_contents('php://input') : null;
        $apiKey  = $_SERVER['HTTP_GX_API_KEY'] ?? $_GET['api_key'] ?? '';

        $ch = curl_init($goUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-GX-Secret: '     . $secret,
                'X-GX-Whitelist: '  . Options::v('go_service_whitelist'),
                'GX-API-KEY: '      . $apiKey,
                'X-Forwarded-For: ' . ($_SERVER['REMOTE_ADDR'] ?? ''),
                'X-Real-IP: '       . ($_SERVER['REMOTE_ADDR'] ?? ''),
            ],
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Fallback logic
        if ($httpCode === 0 || $httpCode === 403 || $httpCode === 404 || $httpCode >= 500) {
            if (Options::v('go_service_fallback') !== 'off') {
                return null;
            }
        }

        if ($response) {
            header('Content-Type: application/json; charset=UTF-8');
            header('X-API-Backend: go');
            http_response_code($httpCode);
            echo $response;
            exit;
        }

        return null;
    }

    /**
     * Verifies API authentication via GX-API-KEY header, query string, or active session.
     *
     * @return bool True if authentication is successful.
     */
    public static function auth()
    {
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

    /**
     * Implements basic IP-based rate limiting for API requests.
     * Uses a stored history in settings to track and limit request counts per hour.
     *
     * @return bool True if the request is within limits.
     */
    public static function rateLimit()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $limit = (int) Options::v('api_rate_limit');
        if ($limit <= 0)
            return true; // Disabled

        $historyRaw = Options::v('api_rate_history');
        if (empty($historyRaw))
            $historyRaw = '[]';
        $history = json_decode($historyRaw, true) ?: [];
        $now = time();
        $window = 3600; // 1 hour window

        // Cleanup old entries
        $history = array_filter($history, fn($t) => $t > ($now - $window));

        // Count requests from this IP in the last window
        $ip_requests = array_filter($history, fn($ip_addr) => $ip_addr === $ip, ARRAY_FILTER_USE_KEY);

        // Simple logic for GeniXCMS v2 architecture:
        // We'll store a dedicated 'api_rate_log' in options for the demo/implementation
        $logRaw = Options::get('api_rate_log', false);
        if (empty($logRaw))
            $logRaw = '[]';
        $log = json_decode($logRaw, true) ?: [];

        if (!isset($log[$ip])) {
            $log[$ip] = ['count' => 1, 'reset' => $now + $window];
        } else {
            if ($now > $log[$ip]['reset']) {
                $log[$ip] = ['count' => 1, 'reset' => $now + $window];
            } else {
                $log[$ip]['count']++;
            }
        }

        // Save back to DB (Note: In high traffic, move to Redis/File Cache)
        Options::update('api_rate_log', json_encode($log));

        // Check if exceeded
        if ($log[$ip]['count'] > $limit) {
            header('X-RateLimit-Limit: ' . $limit);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . $log[$ip]['reset']);
            return false;
        }

        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . ($limit - $log[$ip]['count']));
        header('X-RateLimit-Reset: ' . $log[$ip]['reset']);

        return true;
    }

    /**
     * Sends a JSON response with a specific HTTP status code and exits.
     *
     * @param mixed $data The data to be JSON-encoded.
     * @param int   $code The HTTP status code (default 200).
     */
    public static function response($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Sends a standardized error response.
     *
     * @param int    $code    The HTTP error code.
     * @param string $message Descriptive error message.
     */
    public static function error($code, $message)
    {
        return self::response([
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ], $code);
    }

    /**
     * Sends a standardized success response.
     *
     * @param mixed  $data    The payload data.
     * @param string $message Success message.
     */
    public static function success($data, $message = 'Operation successful')
    {
        return self::response([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 200);
    }
}
