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

class Ajax
{
    private static $_headers_sent = false;

    /**
     * Initializes the AJAX by sending standard JSON headers.
     */
    public static function init()
    {
        if (headers_sent($file, $line)) {
            error_log("DEBUG: Headers already sent in $file on line $line. AJAX will likely fail.");
        }
        if (!self::$_headers_sent) {
            if (ob_get_level() > 0) ob_clean();
            ob_start();
            header('Content-Type: application/json; charset=UTF-8');
            self::$_headers_sent = true;
        }
    }

    /**
     * Dispatches the AJAX request to the appropriate handler.
     * 
     * @param string $resource The AJAX resource (e.g., 'nixomers').
     * @param string $action   The specific action to perform.
     * @param mixed  $param    Additional URL parameters from Router.
     */
    public static function dispatch($resource, $action = null, $param = null)
    {
        // For AJAX, we don't send headers here, let the handler decide
        // self::init(); 

        // Go Backend: Programmatically and dynamically extensible list
        $coreGo = ['posts', 'categories', 'search', 'version', 'stats', 'tags', 'user', 'updates'];
        $dbGo = explode(',', Options::v('go_service_whitelist') ?? '');
        $goSupported = array_unique(array_merge($coreGo, array_map('trim', $dbGo)));
        $goSupported = Hooks::filter('go_supported_resources', $goSupported);
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && (Options::v('api_backend') ?: 'php') === 'go') {
            if (in_array($resource, $goSupported)) {
                $proxy = self::proxyToGo($resource, $action, $param);
                if ($proxy !== null) {
                    return $proxy;
                }
            }
        }

        error_log("DEBUG: Go fallback triggered for resource: $resource, action: $action");

        $resource = str_replace(['_', '-'], ' ', $resource);
        $resource = str_replace(' ', '', ucwords($resource));
        
        $resourceClass = $resource . 'Ajax'; // e.g. NixFulfillmentAjax
        if (class_exists($resourceClass)) {
            $handler = new $resourceClass();
            
            // If action is provided, call that specific method, otherwise call index()
            $method = $action ? $action : 'index';

            if (method_exists($handler, $method)) {
                try {
                    return $handler->$method($param);
                } catch (Exception $e) {
                    return self::error(500, 'Internal Server Error: ' . $e->getMessage());
                }
            } else {
                // Fallback to index if method not found
                if (method_exists($handler, 'index')) {
                    return $handler->index($param);
                }
                return self::error(404, "Action '$method' not found in '$resourceClass'");
            }
        } else {
            error_log("DEBUG: Ajax::dispatch failed to find class $resourceClass or legacy file for $resource");
            // Fallback: Check for legacy control file
            $file = GX_PATH . '/inc/lib/Control/Ajax/' . $resource . '-ajax.control.php';
            if (file_exists($file)) {
                include $file;
                exit;
            }

            error_log("DEBUG: AJAX Resource '$resource' not found. Classes checked: $resourceClass");
            return self::error(404, "AJAX Resource '$resource' not found ($resourceClass)");
        }
    }

    /**
     * Validates the request token.
     *
     * @param mixed $param Additional URL parameters from Router.
     * @return bool True if token is valid.
     */
    public static function auth($param = null)
    {
        $token = $_REQUEST['token'] ?? $_SERVER['HTTP_X_GX_TOKEN'] ?? null;
        
        // Handle SMART_URL token from path
        if (!$token && SMART_URL && $param) {
            $data = Router::scrap($param);
            $token = $data['token'] ?? null;
        }

        if ($token && Token::validate($token)) {
            return true;
        }

        error_log("DEBUG: Ajax::auth failed. Token: " . ($token ?? 'NULL'));
        return false;
    }

    /**
     * Sends a JSON response and exits.
     */
    public static function response($data, $code = 200)
    {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Standards error response.
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
     * Proxies the request to the Go microservice.
     */
    private static function proxyToGo($resource, $action = null, $param = null)
    {
        $serviceUrl = rtrim(Options::v('go_service_url'), '/');
        if (empty($serviceUrl)) {
            return null; // Fallback to PHP if URL not set
        }

        // Plug n Play Mapping: Allow modules to define which table they use in Go
        $proxyArgs = [
            'resource' => $resource,
            'action' => $action,
            'table' => $resource // Default table name matches resource
        ];
        $proxyArgs = Hooks::filter('go_proxy_args', $proxyArgs);

        $targetResource = $proxyArgs['resource'];
        $targetAction = $proxyArgs['action'];
        $targetTable = $proxyArgs['table'];

        $queryParams = $_GET;
        if ($targetResource === 'nixomers' && $targetAction === 'notifications') {
            $userGroup = (int) Session::val('group');
            $username = Session::val('username');
            
            $allowedRoles = ['all'];
            if ($userGroup <= 1) $allowedRoles = array_merge($allowedRoles, ['admin', 'billing', 'fulfillment', 'cs', 'sales']);
            elseif ($userGroup == 2) $allowedRoles[] = 'billing';
            elseif ($userGroup == 3) $allowedRoles[] = 'fulfillment';
            elseif ($userGroup == 4) $allowedRoles[] = 'cs';
            elseif ($userGroup == 5) $allowedRoles[] = 'sales';

            $queryParams['username'] = $username;
            $queryParams['roles'] = implode(',', $allowedRoles);
        }

        $url = "{$serviceUrl}/ajax/{$targetResource}";
        if (!empty($targetAction)) {
            $url .= '/' . $targetAction;
        }

        // Forward query parameters
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Fast timeout for AJAX
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        // Internal Handshake Security + Metadata
        $mediaLocalPath = rtrim(Options::v('media_local_path') ?: 'assets/media', '/');
        $headers = [
            'X-GX-Secret: ' . Options::v('go_service_secret'),
            'X-GX-Whitelist: ' . Options::v('go_service_whitelist'),
            'X-GX-Table: ' . $targetTable,
            'X-GX-Site-URL: ' . Site::$url,
            'X-GX-Site-Root: ' . realpath(GX_PATH),
            'X-GX-MEDIA-URL: ' . rtrim(Site::$url, '/') . '/' . ltrim($mediaLocalPath, '/'),
            'X-GX-Proxy: PHP-Bridge'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            unset($ch);
            return null; // Fallback
        }

        if ($response) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode === 0 || $httpCode === 403 || $httpCode === 404 || $httpCode >= 500) {
                 if (Options::v('go_service_fallback') !== 'off') {
                     unset($ch);
                     return null; 
                 }
            }
            
            // If it's list_posts, we need to decorate the raw data from Go with PHP HTML rendering
            if ($action === 'list_posts' && $resource === 'posts' && $httpCode === 200) {
                $raw = json_decode($response, true);
                if (isset($raw['status']) && $raw['status'] === 'success') {
                    $posts = $raw['data'] ?? [];
                    $rows = [];
                    $group = Session::val('group');
                    $username = Session::val('username');
                    
                    foreach ($posts as $p) {
                        $pObj = (object) $p;
                        $rows[] = Posts::getDashboardRow($pObj, $group, $username, 'post');
                    }
                    
                    $headers = Posts::getDashboardHeaders('post');
                    $response = json_encode([
                        'status' => 'success',
                        'headers' => $headers,
                        'data' => $rows,
                        'total' => $raw['total'] ?? count($rows),
                        'limit' => $raw['limit'] ?? 10,
                        'offset' => $raw['offset'] ?? 0
                    ]);
                }
            }

            // If it's list_users, decorate raw user data into admin table rows
            if ($action === 'list_users' && $resource === 'user' && $httpCode === 200) {
                $raw = json_decode($response, true);
                if (isset($raw['status']) && $raw['status'] === 'success') {
                    $users = $raw['data'] ?? [];
                    $rows = [];

                    foreach ($users as $u) {
                        $uObj = (object) $u;
                        $rows[] = User::getDashboardRow($uObj);
                    }

                    $headers = User::getDashboardHeaders();
                    $response = json_encode([
                        'status' => 'success',
                        'headers' => $headers,
                        'data' => $rows,
                        'total' => $raw['total'] ?? count($rows),
                        'limit' => $raw['limit'] ?? 10,
                        'offset' => $raw['offset'] ?? 0
                    ]);
                }
            }

            // Nixomers Notifications Decoration
            if ($action === 'notifications' && $resource === 'nixomers' && $httpCode === 200) {
                $raw = json_decode($response, true);
                if (isset($raw['status']) && $raw['status'] === 'success') {
                    $latest = $raw['latest'] ?? [];
                    $formatted = [];
                    foreach ($latest as $n) {
                        $nObj = (object) $n;
                        $formatted[] = [
                            'title' => $nObj->title,
                            'message' => $nObj->message,
                            'time' => date('H:i', strtotime($nObj->created_at)),
                            'url' => Site::$url . ADMIN_DIR . '/' . $nObj->url . '&mark_read=' . $nObj->id
                        ];
                    }
                    $response = json_encode([
                        'status' => 'success',
                        'count' => (int) ($raw['count'] ?? 0),
                        'latest' => $formatted
                    ]);
                }
            }

            // Nixomers Orders Decorations
            if ($action === 'list_orders' && $resource === 'nixomers' && $httpCode === 200) {
                $raw = json_decode($response, true);
                if (isset($raw['status']) && $raw['status'] === 'success' && class_exists('NixomersAjax')) {
                    $orders = $raw['data'] ?? [];
                    $rows = [];
                    $nixAjax = new NixomersAjax();
                    
                    $currency = Options::v('nixomers_currency') ?: 'IDR';
                    $mod_url = Site::$url . ADMIN_DIR . '/index.php?page=mods&mod=nixomers';

                    foreach ($orders as $o) {
                        $oObj = (object) $o;
                         $status_color = match ($oObj->status) {
                            'paid', 'completed', 'delivered' => 'success',
                            'cancelled', 'expired' => 'danger',
                            'shipped', 'onprocess' => 'primary',
                            'ready_to_ship' => 'warning',
                            'waiting' => 'info',
                            default => 'secondary'
                        };

                        $pStatus_color = match ($oObj->payment_status) {
                            'paid', 'completed' => 'success',
                            'cancelled', 'expired' => 'danger',
                            'pending' => 'warning',
                            default => 'secondary'
                        };

                        $status_label = match ($oObj->status) {
                            'waiting' => 'waiting process',
                            'ready_to_ship' => 'ready to ship',
                            default => $oObj->status
                        };

                        $pStatus_label = ($oObj->payment_status === 'completed' || $oObj->payment_status === 'paid') ? 'paid' : ($oObj->payment_status ?: 'pending');

                        // Minimal rows to match JS expectations
                        $rows[] = [
                            ['content' => '<input type="checkbox" name="order_id[]" value="' . $oObj->id . '" class="form-check-input ms-4">'],
                            ['content' => '<div><a href="' . $mod_url . '&sel=orderdetail&id=' . ($oObj->order_id ?? '') . '" class="text-decoration-none"><strong class="text-primary">' . ($oObj->order_id ?? 'N/A') . '</strong></a><br><small class="text-muted">ID: #' . $oObj->id . '</small></div>'],
                            ['content' => '<div><strong>' . ($oObj->customer_name ?? 'N/A') . '</strong><br><small class="text-muted">' . ($oObj->customer_phone ?? 'N/A') . '</small></div>'],
                            ['content' => '<div>' . ($oObj->shipping_city ?? '-') . '</div><small class="text-muted">' . ($oObj->shipping_province ?? '-') . '</small>'],
                            ['content' => '<span class="fw-bold">' . $currency . ' ' . number_format($oObj->total, 0) . '</span>'],
                            ['content' => '<span class="badge bg-' . $pStatus_color . ' bg-opacity-10 text-' . $pStatus_color . ' px-3 rounded-pill fw-bold small text-uppercase">' . $pStatus_label . '</span>'],
                            ['content' => '<span class="badge bg-' . $status_color . ' bg-opacity-10 text-' . $status_color . ' px-3 rounded-pill fw-bold small text-uppercase">' . $status_label . '</span>'],
                            ['content' => date('d M Y, H:i', strtotime($oObj->date))],
                            ['content' => '<div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle border shadow-none" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
                                    <li><a class="dropdown-item rounded-3 small fw-bold" href="' . $mod_url . '&sel=orderdetail&id=' . $oObj->order_id . '"><i class="bi bi-eye me-2"></i> View Detail</a></li>
                                </ul>
                            </div>', 'class' => 'text-center pe-4']
                        ];
                    }

                    $headers = [
                        ['content' => '<input type="checkbox" id="selectall" class="form-check-input ms-4" onchange="toggleCheckboxes(this)">', 'width' => '50px'],
                        ['content' => 'Order ID'],
                        ['content' => 'Customer'],
                        ['content' => 'Location'],
                        ['content' => 'Total Amount'],
                        ['content' => 'Payment'],
                        ['content' => 'Order Status'],
                        ['content' => 'Date'],
                        ['content' => 'Action', 'class' => 'text-center pe-4']
                    ];

                    $response = json_encode([
                        'status' => 'success',
                        'headers' => $headers,
                        'data' => $rows,
                        'total' => $raw['total'] ?? count($rows),
                        'limit' => $raw['limit'] ?? 10,
                        'offset' => $raw['offset'] ?? 0
                    ]);
                }
            }

            header('Content-Type: application/json; charset=UTF-8');
            header('X-API-Backend: go-hybrid');
            http_response_code($httpCode);
            echo $response;
            exit;
        }

        return null; // Fallback
    }
}
