<?php

namespace KiriminAja\Services\Shipping;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Models\ShippingFullPriceData;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class FullShippingPrice extends ServiceBase
{

    private ShippingFullPriceData $data;
    private ShippingRepository    $shippingRepo;

    /**
     * @param ShippingFullPriceData $data
     */
    public function __construct(ShippingFullPriceData $data)
    {
        $this->data         = $data;
        $this->shippingRepo = new ShippingRepository;
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {

        if ($this->data->origin == null || $this->data->destination == null || $this->data->weight == null) {
            return self::error(null, "Params origin, destination, weight Can't be blank");
        }

        if (is_string($this->data->origin) || is_string($this->data->destination) || is_string($this->data->weight)) {
            return self::error(null, "Params origin, destination, weight must be an integers");
        }

        try {
            [$status, $data] = $this->shippingRepo->fullShippingPrice($this->data);
            if ($status && $data['status']) {
                return self::success(['details' => $data['details'], 'results' => $data['results'],], "loaded");
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
