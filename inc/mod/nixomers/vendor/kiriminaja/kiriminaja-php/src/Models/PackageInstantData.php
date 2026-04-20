<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;
use KiriminAja\Contracts\WithMappedData;

class PackageInstantData extends ModelBase implements WithMappedData
{
    public string $destination_name;
    public string $destination_phone;
    public string $destination_lat;
    public string $destination_long;
    public string $destination_address;
    public string $destination_address_note;
    public string $origin_name;
    public string $origin_phone;
    public string $origin_lat;
    public string $origin_long;
    public string $origin_address;
    public string $origin_address_note;
    public int $shipping_price;
    public string $item_name;
    public string $item_description;
    public int $item_price;
    public float $item_weight_in_kg;

    /**
     * @return array
     */
    public function getMapped(): array
    {
        $data = $this->toArray();
        $data['item'] = [
            'name' => $this->item_name,
            'type_id' => 1,
            'description' => $this->item_description,
            'price' => $this->item_price,
            'weight_kg' => $this->item_weight_in_kg
        ];
        unset($data['item_name']);
        unset($data['item_description']);
        unset($data['item_price']);
        unset($data['item_weight_in_kg']);
        return $data;
    }
}
