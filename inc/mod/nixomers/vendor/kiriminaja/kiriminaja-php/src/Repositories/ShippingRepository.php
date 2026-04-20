<?php

namespace KiriminAja\Repositories;

use KiriminAja\Base\Traits\ApiBase;
use KiriminAja\Contracts\ShippingContract;
use KiriminAja\Models\PackageData;
use KiriminAja\Models\RequestPickupData;
use KiriminAja\Models\ShippingFullPriceData;
use KiriminAja\Models\ShippingPriceData;

class ShippingRepository implements ShippingContract {

    use ApiBase;

    /**
     * @param \KiriminAja\Models\ShippingPriceData $data
     * @return array
     */
    public function price(ShippingPriceData $data): array {
        return self::api()->post('api/mitra/v6.1/shipping_price', $data->toArray());
    }

    /**
     * @param \KiriminAja\Models\ShippingFullPriceData $data
     * @return array
     */
    public function fullShippingPrice(ShippingFullPriceData $data): array {
        return self::api()->post('api/mitra/v6.1/shipping_price', $data->toArray());
    }

    /**
     * @return array
     */
    public function schedules(): array {
        return self::api()->post('api/mitra/v2/schedules', null);
    }

    /**
     * @param \KiriminAja\Models\RequestPickupData $data
     * @return array
     * @throws \Exception
     */
    public function requestPickup(RequestPickupData $data): array {
        $packages = [];
        foreach ($data->packages as $package) {
            if (!($package instanceof PackageData)) {
                throw new \Exception("Package is not type of PackageData");
            }
            $packages[] = $package->toArray();
        }

        $data->packages = $packages;
        $arrayData = $data->toArray();
        return self::api()->post('api/mitra/v6.1/request_pickup', $arrayData);
    }

    /**
     * @param string $paymentID
     * @return array
     */
    public function payment(string $paymentID): array {
        return self::api()->post('api/mitra/v2/get_payment', ['payment_id' => $paymentID]);
    }

    /**
     * @param string $awb
     * @param string $reason
     * @return array
     */
    public function cancel(string $awb, string $reason): array {
        return self::api()->post('api/mitra/v3/cancel_shipment', [
            "awb"    => $awb,
            "reason" => $reason
        ]);
    }

    /**
     * @param string $orderID
     * @return array
     */
    public function tracking(string $orderID): array {
        return self::api()->post('api/mitra/tracking', ['order_id' => $orderID]);
    }
}
