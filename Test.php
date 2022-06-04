<?php
include_once('./DeliveryRequest.php');
include_once('./Geo.php');
include_once('./Calculator.php');

class Test
{
    function run() {
        $this->doTest("all delivery services", 1);
        $this->doTest("fast delivery service", 1, FastDelivery::ID);
        $this->doTest("slow delivery service", 1, SlowDelivery::ID);
    }

    /**
     * Creates a test list of delivery requests
     * @param int $size The size of the generated array
     * @return DeliveryRequest[]
     */
    static private function generateRequestList(int $size): array
    {
        $list = [];

        for ($i = 0; $i < $size; $i++) {
            $list[] = new DeliveryRequest(
                (Geo::getRandom())->name,
                (Geo::getRandom())->name,
                rand(0, 600000) / 1000.0);
        }

        return $list;
    }

    /**
     * @param string $title
     * @param int $size
     * @param string|null $serviceId
     */
    private static function doTest(string $title, int $size = 10, string $serviceId = null)
    {
        echo "<br><br>Price list for " . $title;
        echo '<br>-----------------------------------------';
        $requestList = self::generateRequestList($size);
        self::log($requestList);
        echo '-----------------------------------------';
        $response = Calculator::getPrices($requestList, $serviceId);
        self::log($response);
        echo '-----------------------------------------';
    }

    private static function log($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
