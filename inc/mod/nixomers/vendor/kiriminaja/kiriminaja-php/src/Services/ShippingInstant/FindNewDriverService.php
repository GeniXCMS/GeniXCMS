<?php

namespace KiriminAja\Services\ShippingInstant;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Repositories\ShippingInstantRepository;
use KiriminAja\Responses\ServiceResponse;

class FindNewDriverService extends ServiceBase
{
    protected string $order_id;
    protected ShippingInstantRepository $shippingInstantRepository;

    /**
     * @param string $order_id
     */
    public function __construct(string $order_id)
    {
        $this->shippingInstantRepository = new ShippingInstantRepository();
        $this->order_id = $order_id;
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        try {
            [$status, $data] = $this->shippingInstantRepository->findNewDriver($this->order_id);
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
