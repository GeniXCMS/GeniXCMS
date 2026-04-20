<?php

namespace KiriminAja\Contracts;

use KiriminAja\Models\PackageInstantData;
use KiriminAja\Models\RequestPickupData;
use KiriminAja\Models\RequestPickupInstantData;
use KiriminAja\Models\ShippingFullPriceData;
use KiriminAja\Models\ShippingPriceData;
use KiriminAja\Models\ShippingPriceInstantData;
use KiriminAja\Responses\ServiceResponse;

interface KiriminAjaContract {

    /**
     * @param int $provinceID
     * @return ServiceResponse
     */
    public static function getCity(int $provinceID): ServiceResponse;

    /**
     * @param string $name
     * @return ServiceResponse
     */
    public static function getDistrictByName(string $name): ServiceResponse;

    /**
     * @param int $cityID
     * @return ServiceResponse
     */
    public static function getDistrict(int $cityID): ServiceResponse;

    /**
     * @return ServiceResponse
     */
    public static function getProvince(): ServiceResponse;

    /**
     * @param array $services
     * @return ServiceResponse
     */
    public static function setWhiteListExpedition(array $services): ServiceResponse;

    /**
     * @param string $url
     * @return ServiceResponse
     */
    public static function setCallback(string $url): ServiceResponse;

    /**
     * @param ShippingPriceData $data
     * @return ServiceResponse
     */
    public static function getPrice(ShippingPriceData $data): ServiceResponse;

    /**
     * @param ShippingPriceInstantData $data
     * @return ServiceResponse
     */
    public static function getPriceInstant(ShippingPriceInstantData $data): ServiceResponse;

    /**
     * @param ShippingFullPriceData $data
     * @return ServiceResponse
     */
    public static function fullShippingPrice(ShippingFullPriceData $data): ServiceResponse;

    /**
     * @return ServiceResponse
     */
    public static function getSchedules(): ServiceResponse;

    /**
     * @param RequestPickupData $data
     * @return ServiceResponse
     */
    public static function requestPickup(RequestPickupData $data): ServiceResponse;

    /**
     * @param RequestPickupInstantData $data
        * @param PackageInstantData ...$package
     * @return ServiceResponse
     */
        public static function requestPickupInstant(RequestPickupInstantData $data, PackageInstantData ...$package): ServiceResponse;

    /**
     * @param string $paymentID
     * @param bool $isInstant
     * @return ServiceResponse
     */
    public static function getPayment(string $paymentID, bool $isInstant = false): ServiceResponse;

    /**
     * @param string $referenceNo
     * @param string $reason
     * @param bool $isInstant
     * @return ServiceResponse
     */
    public static function cancelShipment(string $referenceNo, string $reason, bool $isInstant = false): ServiceResponse;

    /**
     * @param string $orderID
     * @return ServiceResponse
     */
    public static function getTracking(string $orderID): ServiceResponse;

    /**
     * @param string $orderID
     * @return ServiceResponse
     */
    public static function findNewDriver(string $orderID): ServiceResponse;
}
