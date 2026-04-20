<?php

namespace KiriminAja\Repositories;

use KiriminAja\Base\Traits\ApiBase;
use KiriminAja\Contracts\AddressContract;

class AddressRepository implements AddressContract
{

    use ApiBase;

    /**
     * @return array
     */
    public function provinces(): array
    {
        return self::api()->post('api/mitra/province', null);
    }

    /**
     * @param int $provinceId
     * @return array
     */
    public function cities(int $provinceId): array
    {
        return self::api()->post('api/mitra/city', ['provinsi_id' => $provinceId]);
    }

    /**
     * @param int $cityId
     * @return array
     */
    public function districts(int $cityId): array
    {
        return self::api()->post('api/mitra/kecamatan', ['kabupaten_id' => $cityId]);
    }

    /**
     * @param string $name
     * @return array
     */
    public function districtsByName(string $name): array
    {
        return self::api()->post('api/mitra/v2/get_address_by_name', ['search' => $name]);
    }
}
