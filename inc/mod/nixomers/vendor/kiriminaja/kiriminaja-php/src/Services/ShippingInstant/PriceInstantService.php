<?php

namespace KiriminAja\Services\ShippingInstant;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Models\ShippingPriceInstantData;
use KiriminAja\Repositories\ShippingInstantRepository;
use KiriminAja\Responses\ServiceResponse;

class PriceInstantService extends ServiceBase
{
    private ShippingPriceInstantData $data;
    private ShippingInstantRepository $shippingInstantRepository;

    /**
     * @param ShippingPriceInstantData $shippingPriceData
     */
    public function __construct(ShippingPriceInstantData $shippingPriceData)
    {
        $this->data = $shippingPriceData;
        $this->shippingInstantRepository = new ShippingInstantRepository();
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        if (
            is_null($this->data->origin_lat) ||
            is_null($this->data->origin_long) ||
            is_null($this->data->origin_address) ||
            is_null($this->data->destination_lat) ||
            is_null($this->data->destination_long) ||
            is_null($this->data->destination_address) ||
            !is_int($this->data->item_price) ||
            !is_int($this->data->weight) ||
            !is_array($this->data->service)
        ) {
            return self::error(null, "Invalid parameter, please see data inquiry");
        }

        try {
            [$status, $data] = $this->shippingInstantRepository->price($this->data);
            if ($status && $data['status']) {
                return self::success($data['result'], $data['text']);
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
