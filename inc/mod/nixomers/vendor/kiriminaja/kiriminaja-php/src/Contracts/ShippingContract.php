<?php

namespace KiriminAja\Contracts;

use KiriminAja\Models\RequestPickupData;
use KiriminAja\Models\ShippingFullPriceData;
use KiriminAja\Models\ShippingPriceData;

interface ShippingContract {
    /**
     * @param \KiriminAja\Models\ShippingPriceData $data
     * @return mixed
     */
    public function price(ShippingPriceData $data);

    /**
     * @param \KiriminAja\Models\ShippingFullPriceData $data
     * @return mixed
     */
    public function fullShippingPrice(ShippingFullPriceData $data);

    /**
     * @return mixed
     */
    public function schedules();

    /**
     * @param \KiriminAja\Models\RequestPickupData $data
     * @return mixed
     */
    public function requestPickup(RequestPickupData $data);

    /**
     * @param string $paymentID
     * @return mixed
     */
    public function payment(string $paymentID);

    /**
     * @param string $awb
     * @param string $reason
     * @return mixed
     */
    public function cancel(string $awb, string $reason);

    /**
     * @param string $orderID
     * @return mixed
     */
    public function tracking(string $orderID);
}
