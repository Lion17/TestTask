<?php
include_once './Settings.php';
include_once './Emulator.php';
include_once './DeliveryService.php';
include_once './DeliveryInfo.php';

/** Abstract class for all delivery services */
abstract class DeliveryService
{
    /** @var string Unique delivery service code */
    protected $id;
    /** @var string Printed name of the delivery service */
    protected $title;
    /** @var string Delivery service API URI */
    protected $baseUrl;

    /**
     * Returns the delivery price by the specified parameters
     * @param DeliveryRequest $request
     * @return DeliveryInfo Delivery price
     */
    public function getPrice(DeliveryRequest $request): DeliveryInfo {
        $url = $this->makeRequestUrl(
            $request->sourceKladr,
            $request->targetKladr,
            $request->weight);

        $response = $this->request($url);
        if (!$response) {
            return DeliveryInfo::createErrorPrice('Network error');
        }

        try {
            $obj = json_decode($response);

            if ($this::isError($obj)) {
                return DeliveryInfo::createErrorPrice($obj->error);
            }

            return $this->responseConverter($obj);
        } catch (Exception $e) {
            return DeliveryInfo::createErrorPrice('Response conversion error: ' . $e->getMessage());
        }
    }

    /**
     * Forms a request for a specific delivery service
     * @param string $sourceKladr
     * @param string $targetKladr
     * @param float $weight Shipment weight in kg
     * @return string Request string
     */
    abstract function makeRequestUrl(string $sourceKladr, string $targetKladr, float $weight): string;

    /**
     * Converts the delivery server response to a standard form
     * @param $response mixed The object is unique for each delivery service
     * @return DeliveryInfo
     * @throws Exception Any conversion errors
     */
    abstract function responseConverter($response): DeliveryInfo;

    /**
     * Checks the service response for an error
     * If necessary, overridden in inherited classes
     * @param $response
     * @return bool
     */
    abstract static function isError($response): bool;

    public function getTitle(): string {
        return $this->title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $url
     * @return false|string
     */
    private function request(string $url)
    {
        if (Settings::$test) {
            return Emulator::api($url);
        } else {
            return file_get_contents($url);
        }
    }
}

class DeliveryResponse {
    /** @var string */
    public $error;

    public static function error(string $error): DeliveryResponse
    {
        $obj = new DeliveryResponse();
        $obj->error = $error;
        return $obj;
    }
}
