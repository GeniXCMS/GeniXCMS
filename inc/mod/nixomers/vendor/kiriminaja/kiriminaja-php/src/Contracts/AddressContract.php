<?php

namespace KiriminAja\Contracts;

interface AddressContract {
    /**
     * @return mixed
     */
    public function provinces();

    /**
     * @param int $provinceId
     * @return mixed
     */
    public function cities(int $provinceId);

    /**
     * @param int $cityId
     * @return mixed
     */
    public function districts(int $cityId);

    /**
     * @param string $name
     * @return mixed
     */
    public function districtsByName(string $name);
}
