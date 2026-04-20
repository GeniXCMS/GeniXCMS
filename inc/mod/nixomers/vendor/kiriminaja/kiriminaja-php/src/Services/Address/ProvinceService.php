<?php

namespace KiriminAja\Services\Address;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\AddressRepository;
use KiriminAja\Responses\ServiceResponse;

class ProvinceService extends ServiceBase
{

    private AddressRepository $addressRepository;

    public function __construct()
    {
        $this->addressRepository = new AddressRepository;
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->addressRepository->provinces();
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
