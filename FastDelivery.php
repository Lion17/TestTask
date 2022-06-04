<?php
include_once './DeliveryService.php';

class FastDelivery extends DeliveryService
{
    CONST ID = 'fast';

    public function __construct()
    {
        $this->id = self::ID;
        $this->title = 'Быстрая доставка';
        $this->baseUrl = 'https://api.fastdelivery.com/calc';
    }

    function makeRequestUrl(string $sourceKladr, string $targetKladr, float $weight): string
    {
        return sprintf('%s?from=%s&to=%s&cargo=%F.3', $this->baseUrl, $sourceKladr, $targetKladr, $weight);
    }

    /**
     * @param $response FastDeliveryResponse
     * @return DeliveryInfo
     * @throws Exception
     */
    function responseConverter($response): DeliveryInfo
    {
        try {
            $price = $response->price;

            $now = new DateTime();
            $hour = (int) $now->format('G');
            $period = $response->period + ($hour > 17 ? 1 : 0);
            $date = $now->add(new DateInterval(sprintf('P%dD', $period)))->format('Y-m-d');

            return DeliveryInfo::createPrice($price, $date);
        } catch (Exception $e) {
            return DeliveryInfo::createErrorPrice('Invalid conversion: ' . $e->getMessage());
        }
    }

    static function isError($response): bool
    {
        return (bool) $response->error;
    }
}

class FastDeliveryResponse extends DeliveryResponse {
    /** @var float Price */
    public $price;
    /** @var int Number of days from today, but no orders are accepted after 6 p.m. */
    public $period;

    public function __construct(float $price, int $period)
    {
        $this->price = $price;
        $this->period = $period;
    }
}

