<?php

class SlowDelivery extends DeliveryService
{
    CONST ID = 'slow';
    private $basePrice = 150.0;

    public function __construct()
    {
        $this->id = self::ID;
        $this->title = 'Медленная доставка';
        $this->baseUrl = 'https://slowship.ru/price';
    }

    function makeRequestUrl(string $sourceKladr, string $targetKladr, float $weight): string
    {
        return sprintf('%s?w=%F.3&s=%s&t=%s', $this->baseUrl, $weight * 1000.0, $sourceKladr, $targetKladr);
    }

    /**
     * @param $response SlowDeliveryResponse
     * @return DeliveryInfo
     */
    function responseConverter($response): DeliveryInfo
    {
        try {
            $coefficient = (float) $response->coefficient;
            $price = $this->basePrice * $coefficient;

            $date = $response->date;

            return DeliveryInfo::createPrice($price, $date);
        } catch (Exception $e) {
            return DeliveryInfo::createErrorPrice('Invalid conversion: ' . $e->getMessage());
        }
    }

    static function isError($response): bool
    {
        return $response->error !== 'SUCCESS';
    }
}

class SlowDeliveryResponse extends DeliveryResponse {
    /** @var float Coefficient (the final price is the multiplication of the base cost and the coefficient) */
    public $coefficient;
    /** @var string Delivery date in the format 2017-10-20 */
    public $date;

    public function __construct(float $coefficient, string $date, string $error = 'SUCCESS')
    {
        $this->coefficient = $coefficient;
        $this->date = $date;
        $this->error = 'SUCCESS';
    }
}
