<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;

class ShippingPriceInstantData extends ModelBase
{
    /**
     * @var array $service
     */
    public array $service = [];

    public float $origin_lat;
    public float $origin_long;
    public string $origin_address;
    public float $destination_lat;
    public float $destination_long;
    public string $destination_address;

    /**
     * @var int $item_price
     */
    public int $item_price;

    /**
     * @var int $weight
     */
    public int $weight;

    public string $vehicle_name = "motor";
}
