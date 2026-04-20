<?php

namespace KiriminAja\Repositories;

use KiriminAja\Base\Traits\ApiBase;
use KiriminAja\Contracts\ShippingInstantContract;
use KiriminAja\Models\RequestPickupInstantData;
use KiriminAja\Models\ShippingPriceInstantData;

class ShippingInstantRepository implements ShippingInstantContract
{
    use ApiBase;

    /**
     * @param ShippingPriceInstantData $shippingPriceInstantData
     * @return array
     */
    public function price(ShippingPriceInstantData $shippingPriceInstantData): array
    {
        $data = $shippingPriceInstantData;
        $payload = [
            'service' => $data->service,
            'origin' => [
                'long' => $data->origin_long,
                'lat' => $data->origin_lat,
                'address' => $data->origin_address
            ],
            'destination' => [
                'long' => $data->destination_long,
                'lat' => $data->destination_lat,
                'address' => $data->destination_address
            ],
            'vehicle' => [
                'name' => $data->vehicle_name
            ],
            'item_price' => $data->item_price,
            'weight' => $data->weight
        ];

        return self::api(true)->post('open-api/v1/instants/price', $payload);
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function findNewDriver(string $orderId): array
    {
        return self::api(true)->post('open-api/v1/instants/find-new-driver',[
            "order_id" => $orderId
        ]);
    }

    /**
     * @param string $paymentId
     * @return array
     */
    public function getPayment(string $paymentId): array
    {
        return self::api(true)->get("open-api/v1/instants/payment/{$paymentId}");
    }

    /**
     * @param RequestPickupInstantData $requestPickupInstantData
     * @return array
     */
    public function create(RequestPickupInstantData $requestPickupInstantData): array
    {
        return self::api(true)->post('open-api/v1/instants',$requestPickupInstantData->getMapped());
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function cancel(string $orderId): array
    {
        return self::api(true)->delete("open-api/v1/instants/{$orderId}");
    }
}
