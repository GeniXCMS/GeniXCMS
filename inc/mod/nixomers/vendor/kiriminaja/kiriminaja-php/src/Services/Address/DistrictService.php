<?php

namespace KiriminAja\Services\Address;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\AddressRepository;
use KiriminAja\Responses\ServiceResponse;

class DistrictService extends ServiceBase {
    private AddressRepository $addressRepository;
    private int               $cityID;

    /**
     * @param int $cityID
     */
    public function __construct(int $cityID) {
        $this->addressRepository = new AddressRepository;
        $this->cityID            = $cityID;
    }

    /**
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public function call(): ServiceResponse {
        try {
            [$status, $data] = $this->addressRepository->districts($this->cityID);
            if ($status && $data['status']) {
                return self::success($data['datas'], "loaded");
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
