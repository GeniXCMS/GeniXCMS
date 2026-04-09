<?php
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

defined('GX_LIB') or die('Direct Access Not Allowed!');

if (User::access(0)) {

    // AJAX REQUEST HANDLER
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        header('Content-Type: application/json');

        // 1. Core Update Check
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

        // 2. Batch Check: Modules & Themes
        $checkList = [];
        $mods = Mod::modList();
        foreach ($mods as $mId) {
            $checkList[] = ['id' => $mId, 'type' => 'module'];
        }
        $themes = Theme::thmList();
        foreach ($themes as $tId) {
            $checkList[] = ['id' => $tId, 'type' => 'theme'];
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

        // 3. Process Results
        $modUpdates = [];
        foreach ($mods as $mId) {
            $m = Mod::data($mId);
            $v_latest = $m['version'];
            $canUpdate = false;
            $downloadUrl = '#';
            if (isset($updates[$mId])) {
                $latest = $updates[$mId];
                $v_latest = $latest['version'] ?? $m['version'];
                $canUpdate = version_compare($m['version'], $v_latest, '<');
                $downloadUrl = $latest['download_url'] ?? '#';
            }
            $modUpdates[$mId] = [
                'v_latest' => $v_latest,
                'can_update' => $canUpdate,
                'download_url' => $downloadUrl
            ];
        }

        $thmUpdates = [];
        foreach ($themes as $tId) {
            $v_current = '1.0.0'; // Fallback
            $v_latest = $v_current;
            $canUpdate = false;
            $downloadUrl = '#';
            if (isset($updates[$tId])) {
                $latest = $updates[$tId];
                $v_latest = $latest['version'] ?? $v_current;
                $canUpdate = version_compare($v_current, $v_latest, '<');
                $downloadUrl = $latest['download_url'] ?? '#';
            }
            $thmUpdates[$tId] = [
                'v_latest' => $v_latest,
                'can_update' => $canUpdate,
                'download_url' => $downloadUrl
            ];
        }

        // Apply Hooks
        $modUpdates = Hooks::filter('system_updates_modules_ajax', $modUpdates);
        $thmUpdates = Hooks::filter('system_updates_themes_ajax', $thmUpdates);

        echo json_encode([
            'status' => 'success',
            'core' => $core,
            'mods' => $modUpdates,
            'themes' => $thmUpdates
        ]);
        exit;
    }

    // REGULAR PAGE LOAD
    $data['sitetitle'] = _('System Updates');

    // Initial Local Data (No API calls here)
    $data['core'] = [
        'name' => 'GeniXCMS Core',
        'v_current' => System::$version,
        'icon' => 'bi bi-cpu'
    ];

    $data['mods'] = [];
    foreach (Mod::modList() as $mId) {
        $m = Mod::data($mId);
        $data['mods'][] = [
            'id' => $mId,
            'name' => $m['name'],
            'v_current' => $m['version'],
            'icon' => $m['icon'] ?? 'bi bi-puzzle'
        ];
    }

    $data['themes'] = [];
    foreach (Theme::thmList() as $tId) {
        $data['themes'][] = [
            'id' => $tId,
            'name' => Theme::name($tId),
            'v_current' => '1.0.0', // Fallback
            'icon' => 'bi bi-palette'
        ];
    }

    Theme::admin('header', $data);
    System::inc('updates', $data);
    Theme::admin('footer');

} else {
    Control::error('noaccess');
}
