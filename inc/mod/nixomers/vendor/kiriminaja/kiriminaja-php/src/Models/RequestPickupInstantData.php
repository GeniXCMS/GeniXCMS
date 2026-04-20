<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;
use KiriminAja\Contracts\WithMappedData;

class RequestPickupInstantData extends ModelBase implements WithMappedData
{
    public string $service;
    public string $service_type;
    public ?string $insurance_type = null;
    public string $vehicle_name;
    public array $packages = [];

    /**
     * @return array
     */
    public function getMapped(): array
    {
        $data = $this->toArray();
        $data['vehicle'] = [
            'name' => $this->vehicle_name
        ];
        unset($data['vehicle_name']);
        return $data;
    }
}
