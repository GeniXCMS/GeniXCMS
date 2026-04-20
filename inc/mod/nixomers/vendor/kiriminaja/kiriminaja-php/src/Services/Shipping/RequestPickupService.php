<?php

namespace KiriminAja\Services\Shipping;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Models\RequestPickupData;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class RequestPickupService extends ServiceBase
{

    private RequestPickupData  $data;
    private ShippingRepository $shippingRepo;

    /**
     * @param RequestPickupData $data
     */
    public function __construct(RequestPickupData $data)
    {
        $this->data         = $data;
        $this->shippingRepo = new ShippingRepository;
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        if (is_null($this->data->address) || is_null($this->data->phone) || is_null($this->data->name) || is_null($this->data->kecamatan_id) || is_null($this->data->schedule) || $this->data->packages == []) {
            return self::error(null, "Required params can't be blank");
        }

        try {
            [$status, $data] = $this->shippingRepo->requestPickup($this->data);
            if ($status && $data['status']) {
                return self::success([
                    'pickup_number'  => $data['pickup_number'],
                    'payment_status' => $data['payment_status'],
                    'details'        => $data['details'],
                ], "loaded");
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
