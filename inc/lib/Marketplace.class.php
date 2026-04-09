<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Marketplace
{
    public static $apiUrl = 'https://genixcms.web.id/api/v1/marketplace/';

    /**
     * Searches for items in the official GeniXCMS marketplace.
     * Supports filtering by query string, item type, and pagination.
     *
     * @param string $q    Search query string (optional).
     * @param string $type Item type filter: 'theme' or 'module' (default: 'theme').
     * @param int    $page Page number for results (default: 1).
     * @return array       Status and results from the API, or an error message on failure.
     */
    public static function search($q = '', $type = 'theme', $page = 1)
    {
        $endpoint = ($q === '') ? 'items' : 'search';
        $params = [
            'type' => $type,
            'page' => $page
        ];
        if ($q !== '') {
            $params['q'] = $q;
        }
        $url = self::$apiUrl . $endpoint . '?' . http_build_query($params);

        $response = Http::fetch([
            'url' => $url,
            'curl' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
            ]
        ]);

        if ($response === false) {
            return ['status' => 'error', 'message' => _('Failed to connect to marketplace API repository.')];
        }

        $result = json_decode($response, true);
        if ($result === null) {
            return ['status' => 'error', 'message' => _('Received invalid response from marketplace.')];
        }

        return $result;
    }

    /**
     * Downloads and installs a theme or module from the marketplace.
     * Includes automated security scanning and hook execution during extraction.
     *
     * @param int|string $id      The marketplace item ID.
     * @param string     $type    The item type: 'theme' or 'module'.
     * @param string     $license Optional license key for premium items.
     * @param string     $domain  Optional domain name for license validation.
     * @return array              Detailed status of the installation process.
     */
    public static function install($id, $type, $license = '', $domain = '')
    {
        // 1. Fetch item details to get the official download URL from the server
        $params = ['license_key' => $license, 'domain' => $domain];
        $itemInfoUrl = self::$apiUrl . $id . '?' . http_build_query($params);

        $infoResponse = Http::fetch([
            'url' => $itemInfoUrl,
            'curl' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
            ]
        ]);

        $info = json_decode($infoResponse, true);
        if (isset($info['status']) && $info['status'] === 'error') {
            return ['status' => false, 'message' => ($info['message'] ?? _('API Error during installation.'))];
        }

        if (!isset($info['data']['mp_download_url'])) {
            return ['status' => false, 'message' => _('Official download URL not provided by marketplace API.')];
        }

        $downloadUrl = $info['data']['mp_download_url'];

        $targetPath = ($type == 'theme') ? GX_THEME : GX_MOD;
        $cacheDir = GX_PATH . '/inc/cache/';
        $tmpFile = $cacheDir . 'marketplace_tmp_' . $id . '.zip';

        // Ensure cache directory exists
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        // Use CURL to download the file from the official URL
        $ch = curl_init($downloadUrl);
        $fp = fopen($tmpFile, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GeniXCMS/' . System::$version);
        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if ($httpCode == 200 && file_exists($tmpFile)) {
            // SECURITY SCAN (Pre-Extraction)
            $scanResult = Security::scanZip($tmpFile);

            if ($scanResult['status'] === true) {
                $zip = new ZipArchive();
                if ($zip->open($tmpFile) === true) {
                    $dir = explode('/', $zip->statIndex(0)['name']);
                    if (count($dir) == 1) {
                        $zip->close();
                        @unlink($tmpFile);
                        return ['status' => false, 'message' => _('Invalid ZIP structure.')];
                    } else {
                        $zip->extractTo($targetPath);
                        $zip->close();

                        // Run install hooks
                        $hookName = ($type == 'theme') ? 'theme_install_action' : 'module_install_action';
                        Hooks::run($hookName, ['filepath' => $tmpFile, 'filename' => basename($tmpFile)]);

                        @unlink($tmpFile);
                        return ['status' => true, 'message' => _('Installation successful.')];
                    }
                } else {
                    @unlink($tmpFile);
                    return ['status' => false, 'message' => _('Cannot extract files.')];
                }
            } else {
                @unlink($tmpFile);
                return ['status' => false, 'message' => _('Security check failed.'), 'errors' => $scanResult['errors']];
            }
        } else {
            if (file_exists($tmpFile))
                @unlink($tmpFile);
            return ['status' => false, 'message' => _('Failed to download package. HTTP Code: ') . $httpCode];
        }
    }
}
