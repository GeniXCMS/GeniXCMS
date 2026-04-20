<?php

namespace KiriminAja\Repositories;

use KiriminAja\Base\Traits\ApiBase;
use KiriminAja\Contracts\PreferenceContract;

class PreferenceRepository implements PreferenceContract {

    use ApiBase;

    /**
     * @param array $services
     * @return array
     */
    public function setWhiteListExpedition(array $services): array {
        return self::api()->post('api/mitra/v3/set_whitelist_services', ['services' => $services]);
    }

    /**
     * @param string $url
     * @return array
     */
    public function setCallback(string $url): array {
        return self::api()->post('api/mitra/set_callback', ['url' => $url]);
    }
}
