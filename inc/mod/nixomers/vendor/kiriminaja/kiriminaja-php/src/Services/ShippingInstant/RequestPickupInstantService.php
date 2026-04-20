<?php

namespace KiriminAja\Services\ShippingInstant;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Models\PackageInstantData;
use KiriminAja\Models\RequestPickupInstantData;
use KiriminAja\Repositories\ShippingInstantRepository;
use KiriminAja\Responses\ServiceResponse;

class RequestPickupInstantService extends ServiceBase
{
    protected RequestPickupInstantData $data;
    protected ShippingInstantRepository $shippingRepo;

    /**
     * @param RequestPickupInstantData $requestPickupInstantData
     * @param PackageInstantData ...$packageInstantData
     */
    public function __construct(RequestPickupInstantData $requestPickupInstantData, PackageInstantData ...$packageInstantData)
    {
        $this->data = $requestPickupInstantData;
        $this->shippingRepo = new ShippingInstantRepository();
        foreach ($packageInstantData as $data) {
            $this->addPackage($data);
        }
    }

    /**
     * @param PackageInstantData $packageInstantData
     * @return void
     */
    public function addPackage(PackageInstantData $packageInstantData)
    {
        $this->data->packages[] = $packageInstantData->getMapped();
    }

    /**
     * @return ServiceResponse
     */
    public function call(): ServiceResponse
    {
        if (
            is_null($this->data->service) ||
            is_null($this->data->service_type) ||
            is_null($this->data->vehicle_name) ||
            count($this->data->packages) <= 0
        ) {
            return self::error(null, "Required params can't be blank");
        }

        try {
            [$status, $data] = $this->shippingRepo->create($this->data);
            if ($status && $data['status']) {
                return self::success([
                    'payment' => $data['result']['payment'],
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
