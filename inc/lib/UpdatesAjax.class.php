<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * Handles backend update-check AJAX requests through the centralized Ajax dispatcher.
 */
class UpdatesAjax
{
    public function index($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'check';
        if ($action === 'perform_update') {
            return $this->performUpdate($param);
        }
        if ($action === 'install_go_service') {
            return $this->installGoService($param);
        }

        $apiUrl = 'https://genixcms.web.id/api/v1/download/latest';
        $response = Http::fetch([
            'url' => $apiUrl,
            'curl' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
            ]
        ]);
        $apiData = json_decode($response, true);
        $latestCore = $apiData['data'] ?? null;

        $core = [
            'v_latest' => $latestCore['version'] ?? System::$version,
            'can_update' => ($latestCore && version_compare(System::$version, $latestCore['version'], '<')),
            'download_url' => $latestCore['download_url'] ?? '#',
            'changelog' => $latestCore['changelog'] ?? ''
        ];

        $mods = [];
        if (is_dir(GX_MOD)) {
            $handle = dir(GX_MOD);
            while (false !== ($entry = $handle->read())) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $dir = GX_MOD . $entry;
                if (is_dir($dir) && file_exists($dir . '/index.php')) {
                    $mods[] = [
                        'id' => $entry,
                        'version' => self::modVersion($dir . '/index.php')
                    ];
                }
            }
            $handle->close();
        }

        $themes = [];
        if (is_dir(GX_THEME)) {
            $handle = dir(GX_THEME);
            while (false !== ($entry = $handle->read())) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $dir = GX_THEME . $entry;
                if (is_dir($dir) && file_exists($dir . '/themeinfo.php')) {
                    $themes[] = [
                        'id' => $entry,
                        'version' => '1.0.0'
                    ];
                }
            }
            $handle->close();
        }

        $checkList = [];
        foreach ($mods as $m) {
            $checkList[] = ['id' => $m['id'], 'type' => 'module'];
        }
        foreach ($themes as $t) {
            $checkList[] = ['id' => $t['id'], 'type' => 'theme'];
        }

        $batchApiUrl = 'https://genixcms.web.id/api/v1/marketplace/check-update';
        $batchResponse = Http::fetch([
            'url' => $batchApiUrl,
            'curl' => true,
            'curl_options' => [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($checkList),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
            ]
        ]);
        $batchData = json_decode($batchResponse, true);
        $updates = $batchData['data'] ?? [];

        $modUpdates = [];
        foreach ($mods as $m) {
            $v_latest = $m['version'];
            $canUpdate = false;
            $downloadUrl = '#';
            if (isset($updates[$m['id']])) {
                $latest = $updates[$m['id']];
                $v_latest = $latest['version'] ?? $m['version'];
                $canUpdate = version_compare($m['version'], $v_latest, '<');
                $downloadUrl = $latest['download_url'] ?? '#';
            }
            $modUpdates[$m['id']] = [
                'v_latest' => $v_latest,
                'can_update' => $canUpdate,
                'download_url' => $downloadUrl
            ];
        }

        $thmUpdates = [];
        foreach ($themes as $t) {
            $v_current = $t['version'];
            $v_latest = $v_current;
            $canUpdate = false;
            $downloadUrl = '#';
            if (isset($updates[$t['id']])) {
                $latest = $updates[$t['id']];
                $v_latest = $latest['version'] ?? $v_current;
                $canUpdate = version_compare($v_current, $v_latest, '<');
                $downloadUrl = $latest['download_url'] ?? '#';
            }
            $thmUpdates[$t['id']] = [
                'v_latest' => $v_latest,
                'can_update' => $canUpdate,
                'download_url' => $downloadUrl
            ];
        }

        $modUpdates = Hooks::filter('system_updates_modules_ajax', $modUpdates);
        $thmUpdates = Hooks::filter('system_updates_themes_ajax', $thmUpdates);

        return Ajax::response([
            'status' => 'success',
            'core' => $core,
            'mods' => $modUpdates,
            'themes' => $thmUpdates
        ]);
    }

    /**
     * Downloads and extracts the latest core update in-place.
     * Mirrors the Marketplace::install() flow but targets the root GX_PATH.
     *
     * @param mixed $param
     * @return void  Outputs JSON and exits.
     */
    private function performUpdate($param = null)
    {
        // Only super-admins (group <= 1) may perform core updates
        $group = (int) Session::val('group');
        if ($group > 1) {
            return Ajax::error(403, _('Insufficient permissions to perform core update.'));
        }

        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'core';

        // ── CORE UPDATE ──────────────────────────────────────────────
        if ($type === 'core') {
            $apiUrl = 'https://genixcms.web.id/api/v1/download/latest';
            $response = Http::fetch([
                'url' => $apiUrl,
                'curl' => true,
                'curl_options' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
                ]
            ]);
            $apiData = json_decode($response, true);
            $latestCore = $apiData['data'] ?? null;

            if (!$latestCore || empty($latestCore['download_url'])) {
                return Ajax::error(500, _('Could not retrieve download URL from update server.'));
            }

            if (!version_compare(System::$version, $latestCore['version'], '<')) {
                return Ajax::response(['status' => 'success', 'message' => _('Already up-to-date.')]);
            }

            $downloadUrl = $latestCore['download_url'];
            $cacheDir    = GX_PATH . '/assets/cache/';
            $tmpFile     = $cacheDir . 'core_update_' . $latestCore['version'] . '.zip';

            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }

            // Download
            $ch = curl_init($downloadUrl);
            $fp = fopen($tmpFile, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'GeniXCMS/' . System::$version);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            unset($ch);
            fclose($fp);

            if ($httpCode !== 200 || !file_exists($tmpFile)) {
                @unlink($tmpFile);
                return Ajax::error(500, _('Failed to download core update package. HTTP: ') . $httpCode);
            }

            // Security scan
            $scanResult = Security::scanZip($tmpFile);
            if ($scanResult['status'] !== true) {
                @unlink($tmpFile);
                return Ajax::error(500, _('Security scan failed: ') . implode(', ', $scanResult['errors']));
            }

            // Extract — core ZIPs typically have a top-level folder (e.g. GeniXCMS-2.x.x/)
            // We strip that folder and extract directly into GX_PATH
            $zip = new ZipArchive();
            if ($zip->open($tmpFile) !== true) {
                @unlink($tmpFile);
                return Ajax::error(500, _('Cannot open update ZIP file.'));
            }

            // Detect top-level folder prefix to strip
            $firstEntry = $zip->statIndex(0);
            $prefix     = '';
            if ($firstEntry) {
                $parts = explode('/', $firstEntry['name']);
                if (count($parts) > 1) {
                    $prefix = $parts[0] . '/';
                }
            }

            $extractOk = true;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->statIndex($i);
                $name  = $entry['name'];

                // Strip top-level folder
                if ($prefix !== '' && strpos($name, $prefix) === 0) {
                    $name = substr($name, strlen($prefix));
                }

                if ($name === '' || substr($name, -1) === '/') {
                    // Directory entry — ensure it exists
                    $dir = GX_PATH . '/' . $name;
                    if ($name !== '' && !is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    continue;
                }

                $targetFile = GX_PATH . '/' . $name;
                $targetDir  = dirname($targetFile);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $content = $zip->getFromIndex($i);
                if (file_put_contents($targetFile, $content) === false) {
                    $extractOk = false;
                    break;
                }
            }
            $zip->close();
            @unlink($tmpFile);

            if (!$extractOk) {
                return Ajax::error(500, _('Failed to write some files during extraction.'));
            }

            Hooks::run('core_update_complete', ['version' => $latestCore['version']]);

            return Ajax::response([
                'status'  => 'success',
                'message' => sprintf(_('Core updated to v%s successfully.'), $latestCore['version']),
                'version' => $latestCore['version']
            ]);
        }

        // ── MODULE / THEME UPDATE ────────────────────────────────────
        $id   = isset($_GET['id'])   ? Typo::cleanX($_GET['id'])   : '';
        if (empty($id)) {
            return Ajax::error(400, _('Missing item ID.'));
        }

        $result = Marketplace::install($id, $type);
        if ($result['status']) {
            return Ajax::response(['status' => 'success', 'message' => $result['message']]);
        }
        return Ajax::error(500, $result['message']);
    }

    /**
     * Downloads and extracts the Go microservice package into go-service/ at root.
     */
    private function installGoService($param = null): void
    {
        $group = (int) Session::val('group');
        if ($group > 1) {
            Ajax::error(403, _('Insufficient permissions.'));
            return;
        }

        $downloadUrl = isset($_GET['url']) ? Typo::cleanX($_GET['url']) : '';
        if (empty($downloadUrl) || !filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
            Ajax::error(400, _('Invalid download URL.'));
            return;
        }

        // Only allow downloads from trusted domain
        $host = parse_url($downloadUrl, PHP_URL_HOST);
        if ($host !== 'genixcms.web.id') {
            Ajax::error(403, _('Download URL must be from genixcms.web.id.'));
            return;
        }

        $cacheDir = GX_PATH . '/assets/cache/';
        $tmpFile  = $cacheDir . 'go-service-download.zip';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        // Download
        $ch = curl_init($downloadUrl);
        $fp = fopen($tmpFile, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GeniXCMS/' . System::$version);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        unset($ch);
        fclose($fp);

        if ($httpCode !== 200 || !file_exists($tmpFile)) {
            @unlink($tmpFile);
            Ajax::error(500, _('Failed to download Go service package. HTTP: ') . $httpCode);
            return;
        }

        // Extract
        $zip = new ZipArchive();
        if ($zip->open($tmpFile) !== true) {
            @unlink($tmpFile);
            Ajax::error(500, _('Cannot open Go service ZIP file.'));
            return;
        }

        $targetDir = GX_PATH . '/go-service/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Detect and strip top-level folder prefix
        $firstEntry = $zip->statIndex(0);
        $prefix = '';
        if ($firstEntry) {
            $parts = explode('/', $firstEntry['name']);
            if (count($parts) > 1) {
                $prefix = $parts[0] . '/';
            }
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->statIndex($i);
            $name  = $entry['name'];
            if ($prefix !== '' && strpos($name, $prefix) === 0) {
                $name = substr($name, strlen($prefix));
            }
            if ($name === '' || substr($name, -1) === '/') {
                $dir = $targetDir . $name;
                if ($name !== '' && !is_dir($dir)) mkdir($dir, 0777, true);
                continue;
            }
            $targetFile = $targetDir . $name;
            $targetDirPath = dirname($targetFile);
            if (!is_dir($targetDirPath)) mkdir($targetDirPath, 0777, true);
            file_put_contents($targetFile, $zip->getFromIndex($i));
        }
        $zip->close();
        @unlink($tmpFile);

        Ajax::response(['status' => 'success', 'message' => _('Go service installed successfully.')]);
    }

    private static function modVersion($file)
    {
        $content = file_get_contents($file);
        if ($content === false) {
            return '0.0.0';
        }

        if (preg_match('/\* Version: (.*)\s\*/Us', $content, $matches)) {
            return trim($matches[1]);
        }

        return '0.0.0';
    }
}
