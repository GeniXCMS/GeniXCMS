<?php

namespace KiriminAja\Services\Preference;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\PreferenceRepository;
use KiriminAja\Responses\ServiceResponse;

class SetWhitelistExpeditionService extends ServiceBase {

    private array                $services;
    private PreferenceRepository $preferenceRepo;

    /**
     * @param array $services
     */
    public function __construct(array $services) {
        $this->services = $services;
        $this->preferenceRepo = new PreferenceRepository;
    }


    /**
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public function call(): ServiceResponse {

        if(count(array_filter($this->services)) != count($this->services)) {
            return self::error(null, "Array of value Can't be empty");
        }

        try {
            [$status, $data] = $this->preferenceRepo->setWhiteListExpedition($this->services);
            if ($status && $data['status']) {
                return self::success(null, $data['text']);
            }
            if (isset($data['status']) && !$data['status']) {
                return self::error(null, $data['text']);
            }
            return self::error(null, json_encode($data));
        } catch (\Throwable $th) {
            return self::error(null, $th->getMessage());
        }
    }
}
