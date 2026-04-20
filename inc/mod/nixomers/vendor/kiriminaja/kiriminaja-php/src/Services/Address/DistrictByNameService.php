<?php

namespace KiriminAja\Services\Address;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\AddressRepository;
use KiriminAja\Responses\ServiceResponse;
use KiriminAja\Utils\Validator;

class DistrictByNameService extends ServiceBase
{
    private AddressRepository $addressRepository;
    private string            $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->addressRepository = new AddressRepository;
        $this->name              = $name;
    }

    /**
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public function call(): ServiceResponse
    {

        try {
            [$status, $data] = $this->addressRepository->districtsByName($this->name);
            if ($status && $data['status']) {
                return self::success($data['data'], "loaded");
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
