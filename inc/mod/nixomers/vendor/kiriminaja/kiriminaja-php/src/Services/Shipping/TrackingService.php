<?php

namespace KiriminAja\Services\Shipping;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class TrackingService extends ServiceBase
{

    private string             $orderID;
    private ShippingRepository $shippingRepo;

    /**
     * @param string $orderID
     */
    public function __construct(string $orderID)
    {
        $this->orderID      = $orderID;
        $this->shippingRepo = new ShippingRepository;
    }


    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingRepo->tracking($this->orderID);
            if ($status && $data['status']) {
                return self::success([
                    'status_code' => $data['status_code'],
                    'details'     => $data['details'],
                    'histories'   => $data['histories'],
                ], $data['text']);
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
