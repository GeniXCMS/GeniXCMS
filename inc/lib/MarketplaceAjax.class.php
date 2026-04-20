<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class MarketplaceAjax
{
    /**
     * GeniXCMS - Content Management System
     */
    public function index($param = null)
    {
        $action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'search';
        if (method_exists($this, $action)) {
            return $this->$action($param);
        }
        return Ajax::error(404, _('Unknown action'));
    }

    /**
     * Search marketplace
     */
    public function search($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::error(401, _('Unauthorized'));
        }

        $q = isset($_GET['q']) ? Typo::cleanX($_GET['q']) : '';
        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'theme';
        $page = isset($_GET['page']) ? Typo::int($_GET['page']) : 1;

        try {
            $result = Marketplace::search($q, $type, $page);
            return Ajax::response($result);
        } catch (Exception $e) {
            return Ajax::error(500, $e->getMessage());
        }
    }

    /**
     * Install from marketplace
     */
    public function install($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::error(401, _('Unauthorized'));
        }

        $id = isset($_GET['id']) ? Typo::int($_GET['id']) : 0;
        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'theme';
        $license = isset($_GET['license_key']) ? Typo::cleanX($_GET['license_key']) : '';
        $domain = isset($_GET['domain']) ? Typo::cleanX($_GET['domain']) : '';

        if ($id > 0) {
            try {
                $result = Marketplace::install($id, $type, $license, $domain);
                return Ajax::response($result);
            } catch (Exception $e) {
                return Ajax::error(500, $e->getMessage());
            }
        } else {
            return Ajax::error(400, _('Invalid ID'));
        }
    }

    /**
     * Internal auth check
     */
    private function _auth($param = null)
    {
        $url = Site::canonical();
        if (!Http::validateUrl($url) || !User::access(1)) {
            return false;
        }

        $data = Router::scrap($param);
        $gettoken = (defined('SMART_URL') && SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');

        return (true === Token::validate($gettoken, true));
    }
}
