<?php

namespace KiriminAja\Services\ShippingInstant;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingInstantRepository;
use KiriminAja\Responses\ServiceResponse;

class CancelShippingInstantService extends ServiceBase
{

    private string $orderId;
    private ShippingInstantRepository $shippingRepo;

    /**
     * @param string $orderId
     */
    public function __construct(string $orderId)
    {
        $this->orderId          = $orderId;
        $this->shippingRepo = new ShippingInstantRepository();
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingRepo->cancel($this->orderId);
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
