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
