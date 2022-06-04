<?php
include_once ('./DeliveryServices.php');

/** Class for calculating delivery prices */
class Calculator
{
    /**
     * @param DeliveryRequest[] $requestList
     * @param string $serviceId
     * @return ServicePrices[]
     */
    static function getPrices(array $requestList, string $serviceId = null): array {
        $prices = [];

        $services = DeliveryServices::instance();
        if (is_null($serviceId)) {
            for ($service = $services->first(); !$services->isLast(); $service = $services->next()) {
                $prices[] = self::getServicePrices($requestList, $service);
            }
        } else {
            $service = $services->getById($serviceId);
            if (!is_null($service)) {
                $prices[] = self::getServicePrices($requestList, $service);
            }
        }


        return $prices;
    }

    /**
     * @param DeliveryRequest[] $requestList
     * @param DeliveryService $service
     * @return ServicePrices
     */
    static private function getServicePrices(array $requestList, DeliveryService $service): ServicePrices
    {
        $prices = [];

        foreach ($requestList as $request) {
            $prices[] = $service->getPrice($request);
        }

        return new ServicePrices($service->getId(), $prices);
    }

}

/** List of prices for the specified delivery service */
class ServicePrices {
    /** @var string Unique delivery service code */
    public $serviceId;
    /** @var DeliveryInfo[] List of calculated prices from the specified service */
    public $prices;

    /**
     * @param string $serviceId
     * @param DeliveryInfo[] $prices
     */
    public function __construct(string $serviceId, array $prices)
    {
        $this->serviceId = $serviceId;
        $this->prices = $prices;
    }
}
