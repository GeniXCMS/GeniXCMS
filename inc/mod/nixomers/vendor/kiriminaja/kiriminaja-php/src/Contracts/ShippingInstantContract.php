<?php

namespace KiriminAja\Contracts;

use KiriminAja\Models\RequestPickupInstantData;
use KiriminAja\Models\ShippingPriceInstantData;

interface ShippingInstantContract
{
    /**
     * @param ShippingPriceInstantData $shippingPriceInstantData
     * @return array
     */
    public function price(ShippingPriceInstantData $shippingPriceInstantData): array;

    /**
     * @param string $orderId
     * @return array
     */
    public function findNewDriver(string $orderId): array;

    /**
     * @param string $paymentId
     * @return array
     */
    public function getPayment(string $paymentId): array;

    /**
     * @param RequestPickupInstantData $requestPickupInstantData
     * @return array
     */
    public function create(RequestPickupInstantData $requestPickupInstantData): array;

    /**
     * @param string $orderId
     * @return array
     */
    public function cancel(string $orderId): array;
}
