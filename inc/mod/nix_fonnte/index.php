<?php
/**
 * Name: Nix Fonnte Integration
 * Desc: WhatsApp Notification provider for Nixomers using Fonnte.com
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-whatsapp
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// 1. Register Fonnte as a WhatsApp Provider in Nixomers
Hooks::attach('nix_notification_wa_providers', function($args) {
    $providers = $args[0] ?? [];
    $providers['fonnte'] = 'Fonnte (WhatsApp Gateway)';
    return [$providers]; // Return as array for filter
});

// 2. Add Fonnte Specific Settings to Nixomers UI
Hooks::attach('nix_notification_settings_wa_fonnte', function($args) {
    return '
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label small fw-bold">Fonnte API Token</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                    <input type="text" name="nix_notif_wa_fonnte_token" class="form-control" value="'.Options::v('nix_notif_wa_fonnte_token').'" placeholder="Enter your Fonnte token here">
                </div>
                <div class="form-text mt-2 text-muted">Get your token from <a href="https://fonnte.com" target="_blank" class="text-primary text-decoration-none">Fonnte.com</a> dashboard.</div>
            </div>
        </div>
    ';
});

// 3. Handle Saving Fonnte Settings
Hooks::attach('nix_notification_save', function($args) {
    if (isset($_POST['nix_notif_wa_fonnte_token'])) {
        Options::update('nix_notif_wa_fonnte_token', Typo::cleanX($_POST['nix_notif_wa_fonnte_token']));
    }
});

/**
 * Fonnte Helper Class
 * Handles actual API communication
 */
if (!class_exists('NixFonnte')) {
    class NixFonnte {
        
        /**
         * Send WhatsApp Message via Fonnte API
         * @param string $target Phone number or group ID
         * @param string $message The message body
         * @return array Response status and message
         */
        public static function send($target, $message) {
            $token = Options::v('nix_notif_wa_fonnte_token');
            if (empty($token)) {
                return ['status' => false, 'message' => 'Fonnte API Token is not configured.'];
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62', // Default Indonesia
                ),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $token"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return ['status' => false, 'message' => 'Curl Error: ' . $err];
            } else {
                $res = json_decode($response, true);
                return [
                    'status' => $res['status'] ?? false,
                    'message' => $res['reason'] ?? ($res['status'] ? 'Message sent successfully' : 'Failed to send message')
                ];
            }
        }
    }
}
