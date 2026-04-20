<?php

namespace KiriminAja\Services;

use KiriminAja\Base\ServiceBase;
use KiriminAja\Contracts\KiriminAjaContract;
use KiriminAja\Contracts\ServiceContract;
use KiriminAja\Models\PackageInstantData;
use KiriminAja\Models\RequestPickupData;
use KiriminAja\Models\RequestPickupInstantData;
use KiriminAja\Models\ShippingFullPriceData;
use KiriminAja\Models\ShippingPriceData;
use KiriminAja\Models\ShippingPriceInstantData;
use KiriminAja\Responses\ServiceResponse;
use KiriminAja\Services\Address\CityService;
use KiriminAja\Services\Address\DistrictByNameService;
use KiriminAja\Services\Address\DistrictService;
use KiriminAja\Services\Address\ProvinceService;
use KiriminAja\Services\Preference\SetCallbackService;
use KiriminAja\Services\Preference\SetWhitelistExpeditionService;
use KiriminAja\Services\Shipping\CancelShippingService;
use KiriminAja\Services\Shipping\FullShippingPrice;
use KiriminAja\Services\Shipping\GetPaymentService;
use KiriminAja\Services\Shipping\PriceService;
use KiriminAja\Services\Shipping\RequestPickupService;
use KiriminAja\Services\Shipping\ScheduleService;
use KiriminAja\Services\Shipping\TrackingService;
use KiriminAja\Services\ShippingInstant\CancelShippingInstantService;
use KiriminAja\Services\ShippingInstant\FindNewDriverService;
use KiriminAja\Services\ShippingInstant\GetPaymentInstantService;
use KiriminAja\Services\ShippingInstant\PriceInstantService;
use KiriminAja\Services\ShippingInstant\RequestPickupInstantService;

class KiriminAja implements KiriminAjaContract
{

    /**
     * Call service when called
     * @param ServiceContract $service
     * @return ServiceResponse
     */
    private static function call(ServiceContract $service): ServiceResponse
    {
        return $service->call();
    }

    /**
     * @param int $provinceID
     * @return ServiceResponse
     */
    public static function getCity(int $provinceID): ServiceResponse
    {
        return self::call((new CityService($provinceID)));
    }

    /**
     * @param string $name
     * @return ServiceResponse
     */
    public static function getDistrictByName(string $name): ServiceResponse
    {
        return self::call((new DistrictByNameService($name)));
    }

    /**
     * @param int $cityID
     * @return ServiceResponse
     */
    public static function getDistrict(int $cityID): ServiceResponse
    {
        return self::call((new DistrictService($cityID)));
    }

    /**
     * @return ServiceResponse
     */
    public static function getProvince(): ServiceResponse
    {
        return self::call((new ProvinceService()));
    }

    /**
     * @param array $services
     * @return ServiceResponse
     */
    public static function setWhiteListExpedition(array $services): ServiceResponse
    {
        return self::call((new SetWhitelistExpeditionService($services)));
    }

    /**
     * @param string $url
     * @return ServiceResponse
     */
    public static function setCallback(string $url): ServiceResponse
    {
        return self::call((new SetCallbackService($url)));
    }

    /**
     * @param ShippingPriceData $data
     * @return ServiceResponse
     */
    public static function getPrice(ShippingPriceData $data): ServiceResponse
    {
        return self::call((new PriceService($data)));
    }

    /**
     * @param ShippingPriceInstantData $data
     * @return ServiceResponse
     */
    public static function getPriceInstant(ShippingPriceInstantData $data): ServiceResponse
    {
        return self::call((new PriceInstantService($data)));
    }

    /**
     * @param ShippingFullPriceData $data
     * @return ServiceResponse
     */
    public static function fullShippingPrice(ShippingFullPriceData $data): ServiceResponse
    {
        return self::call((new FullShippingPrice($data)));
    }

    /**
     * @return ServiceResponse
     */
    public static function getSchedules(): ServiceResponse
    {
        return self::call((new ScheduleService()));
    }

    /**
     * @param RequestPickupData $data
     * @return ServiceResponse
     */
    public static function requestPickup(RequestPickupData $data): ServiceResponse
    {
        return self::call((new RequestPickupService($data)));
    }

    /**
     * @param RequestPickupInstantData $data
     * @param PackageInstantData ...$package
     * @return ServiceResponse
     */
    public static function requestPickupInstant(RequestPickupInstantData $data, PackageInstantData ...$package): ServiceResponse
    {
        return self::call((new RequestPickupInstantService($data, ...$package)));
    }

    /**
     * @param string $paymentID
     * @param bool $isInstant
     * @return ServiceResponse
     */
    public static function getPayment(string $paymentID, bool $isInstant = false): ServiceResponse
    {
        return self::call(
            $isInstant ?
                (new GetPaymentInstantService($paymentID)) :
                (new GetPaymentService($paymentID))
        );
    }

    /**
     * @param string $referenceNo
     * @param string $reason
     * @param bool $isInstant
     * @return ServiceResponse
     */
    public static function cancelShipment(string $referenceNo, string $reason, bool $isInstant = false): ServiceResponse
    {
        return self::call(
            $isInstant ?
                (new CancelShippingInstantService($referenceNo)) :
                (new CancelShippingService($referenceNo, $reason))
        );
    }

    /**
     * @param string $orderID
     * @return ServiceResponse
     */
    public static function getTracking(string $orderID): ServiceResponse
    {
        return self::call((new TrackingService($orderID)));
    }

    /**
     * @param string $orderID
     * @return ServiceResponse
     */
    public static function findNewDriver(string $orderID): ServiceResponse
    {
        return self::call((new FindNewDriverService($orderID)));
    }
}
