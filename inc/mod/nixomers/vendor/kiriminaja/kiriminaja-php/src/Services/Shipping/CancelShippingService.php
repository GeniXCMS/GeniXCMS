<?php

namespace KiriminAja\Services\Shipping;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class CancelShippingService extends ServiceBase
{

    private string $awb, $reason;
    private ShippingRepository $shippingRepo;

    /**
     * @param string $awb
     * @param string $reason
     */
    public function __construct(string $awb, string $reason)
    {
        $this->awb          = $awb;
        $this->reason       = $reason;
        $this->shippingRepo = new ShippingRepository;
    }


    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingRepo->cancel($this->awb, $this->reason);
            if ($status && $data['status']) {
                return self::success($data['data'], $data['text']);
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
