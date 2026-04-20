<?php

namespace KiriminAja\Services\Shipping;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class GetPaymentService extends ServiceBase
{

    private string             $paymentID;
    private ShippingRepository $shippingRepo;

    /**
     * @param string $paymentID
     */
    public function __construct(string $paymentID)
    {
        $this->paymentID    = $paymentID;
        $this->shippingRepo = new ShippingRepository;
    }


    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingRepo->payment($this->paymentID);
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
