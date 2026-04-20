<?php

namespace KiriminAja\Services\ShippingInstant;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingInstantRepository;
use KiriminAja\Repositories\ShippingRepository;
use KiriminAja\Responses\ServiceResponse;

class GetPaymentInstantService extends ServiceBase
{

    private string             $paymentID;
    private ShippingInstantRepository $shippingRepo;

    /**
     * @param string $paymentID
     */
    public function __construct(string $paymentID)
    {
        $this->paymentID    = $paymentID;
        $this->shippingRepo = new ShippingInstantRepository();
    }


    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingRepo->getPayment($this->paymentID);
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
