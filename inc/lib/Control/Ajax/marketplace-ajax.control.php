<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 2.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(1)) {
    header('Content-Type: application/json');

    // We expect action as a GET parameter
    $action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'search';

    // Token validation - handle both SMART_URL (path) and regular GET token
    $data = Router::scrap($param);
    $gettoken = (defined('SMART_URL') && SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');


    if (!Token::validate($gettoken, true)) {
        echo json_encode(['status' => 'error', 'message' => _('Token not exist or invalid'), 'debug_token' => $gettoken]);
        exit;
    }

    switch ($action) {
        case 'search':
            $q = isset($_GET['q']) ? Typo::cleanX($_GET['q']) : '';
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'theme';
            $page = isset($_GET['page']) ? Typo::int($_GET['page']) : 1;

            try {
                $result = Marketplace::search($q, $type, $page);
                echo json_encode($result);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;

        case 'install':
            $id = isset($_GET['id']) ? Typo::int($_GET['id']) : 0;
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'theme';
            $license = isset($_GET['license_key']) ? Typo::cleanX($_GET['license_key']) : '';
            $domain = isset($_GET['domain']) ? Typo::cleanX($_GET['domain']) : '';

            if ($id > 0) {
                try {
                    $result = Marketplace::install($id, $type, $license, $domain);
                    echo json_encode($result);
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => _('Invalid ID')]);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => _('Unknown action')]);
            break;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => _('No Access')]);
}
exit;
