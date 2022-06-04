<?php
include_once './FastDelivery.php';
include_once './SlowDelivery.php';

/** A class for working with the list of available delivery services */
class DeliveryServices {
    /** @var DeliveryServices */
    static $services;
    /** @var DeliveryService[] */
    private $list;

    private function __construct()
    {
        $this->list = [
            new FastDelivery(),
            new SlowDelivery()
        ];
    }

    static function instance(): DeliveryServices {
        if (self::$services === null) {
            self::$services = new DeliveryServices();
        }
        return self::$services;
    }

    /** Returns the first service in the list */
    function first(): DeliveryService {
        reset( $this->list);
        return current($this->list);
    }

    /** Returns the next service in the list
     * @return DeliveryServices|false
     */
    function next() {
        return next($this->list);
    }

    public function isLast(): bool
    {
        return current($this->list) === false;
    }

    /**
     * Returns the service by its identifier
     * @param string $id
     * @return null|DeliveryService
     */
    function getById(string $id) {
        foreach ($this->list as $service) {
            if ($service->getId() === $id) { return $service; }
        }

        return null;
    }
}
