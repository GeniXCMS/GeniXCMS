<?php
/**
 * NixShipping Class
 * Handles Shipping Rate calculation and Regional Search logic
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixShipping
{
    /**
     * AJAX endpoint for searching Indonesian regions
     */
    public static function ajaxSearchRegion()
    {
        $type = Typo::cleanX($_GET['type'] ?? '');
        $parent = Typo::cleanX($_GET['parent'] ?? '');

        $token = Options::v('nix_apicoid_token');
        if (empty($token)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'API.CO.ID Token not configured']);
            exit;
        }

        $url = "";
        switch ($type) {
            case 'province':
                $url = "https://use.api.co.id/regional/indonesia/provinces";
                break;
            case 'city':
            case 'regency':
                if (empty($parent)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Parent code is required']);
                    exit;
                }
                $url = "https://use.api.co.id/regional/indonesia/provinces/{$parent}/regencies";
                break;
            case 'district':
                if (empty($parent)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Parent code is required']);
                    exit;
                }
                $url = "https://use.api.co.id/regional/indonesia/regencies/{$parent}/districts";
                break;
            case 'village':
                if (empty($parent)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Parent code is required']);
                    exit;
                }
                $url = "https://use.api.co.id/regional/indonesia/districts/{$parent}/villages";
                break;
            default:
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Invalid region type']);
                exit;
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-co-id: {$token}",
                "Accept: application/json"
            ]);

            $response = curl_exec($ch);
            unset($ch);

            // API.CO.ID returns "code" but our JS expects "id"
            $data = json_decode($response, true);
            if (isset($data['is_success']) && $data['is_success'] && isset($data['data'])) {
                foreach ($data['data'] as &$item) {
                    $item['id'] = $item['code'];
                }
                $response = json_encode($data);
            }

            header('Content-Type: application/json');
            echo $response;
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX endpoint for fetching available shipping rates
     */
    public static function ajaxFetchRates()
    {
        $dest_village_id = Typo::cleanX($_GET['village_id'] ?? '');
        if (empty($dest_village_id)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Destination village ID required']);
            exit;
        }

        // Calculate total weight - prefer explicit weight param (from POS/admin), fallback to session cart
        $weight = 0;
        if (!empty($_GET['weight'])) {
            $weight = (float) $_GET['weight'];
        } else {
            foreach (($_SESSION['nix_cart'] ?? []) as $id => $qty) {
                $p_weight = (float) (Posts::getParam('weight', $id) ?: 0);
                $weight += ($p_weight * $qty);
            }
        }

        // Minimum 1kg (1000gr) if weight is too low or not set
        if ($weight < 10) $weight = 1000;

        $res = self::getRatesFromApiCoId($dest_village_id, $weight);

        // Apply enabled couriers filter from admin settings
        if ($res['status'] === true && !empty($res['data'])) {
            $enabledRaw = Options::v('nix_enabled_couriers');
            if (!empty($enabledRaw)) {
                // Stored as uppercase comma-separated: "JNE,SICEPAT,SAP"
                $enabledCodes = array_map('strtoupper', array_map('trim', explode(',', $enabledRaw)));
                $res['data'] = array_values(array_filter($res['data'], function($item) use ($enabledCodes) {
                    // courier_name holds the courier_code from API (e.g. JNE, JNECargo, SiCepat)
                    return in_array(strtoupper($item['courier_name']), $enabledCodes);
                }));
            }
        }

        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    /**
     * Internal helper to call API.CO.ID Shipping Engine
     */
    public static function getRatesFromApiCoId($dest_village_id, $weight)
    {
        $token = Options::v('nix_apicoid_token');
        $origin_village_id = Options::v('nix_orig_village');

        if (empty($token) || empty($origin_village_id)) {
            return ['status' => false, 'message' => 'API.CO.ID Configuration incomplete (Token/Origin ID missing)'];
        }

        $url = "https://use.api.co.id/expedition/shipping-cost";
        $kg = max(1, ceil($weight / 1000));
        
        $params = http_build_query([
            'origin_village_code' => $origin_village_id,
            'destination_village_code' => $dest_village_id,
            'weight' => $kg
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$params}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-co-id: {$token}",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        unset($ch);

        $data = json_decode($response, true);
        if ($httpCode === 200 && isset($data['is_success']) && $data['is_success'] === true) {
            $formatted_data = [];
            // API.CO.ID returns an object in data, with a "couriers" array
            if (isset($data['data']['couriers']) && is_array($data['data']['couriers'])) {
                foreach ($data['data']['couriers'] as $c) {
                    $formatted_data[] = [
                        'courier_name' => $c['courier_code'] ?? 'Unknown',
                        'service_name' => $c['courier_name'] ?? '',
                        'cost' => $c['price'] ?? 0,
                        'etd' => $c['estimation'] ?? '-',
                        'engine' => 'apicoid'
                    ];
                }
            }
            return ['status' => true, 'data' => $formatted_data];
        } else {
            return ['status' => false, 'message' => $data['message'] ?? 'Failed to fetch rates from API.CO.ID'];
        }
    }
}
