<?php

/** A class for storing information about a calculated delivery */
class DeliveryInfo
{
    /** @var float Calculated delivery price */
    private $price;
    /** @var string|null Delivery date in the format 2017-10-20 */
    private $date;
    /** @var string|null Error message on unsuccessful calculation */
    private $error;

    /**
     * @param float $price
     * @param string $date
     * @param string $error
     */
    public function __construct(float $price, string $date, string $error = null)
    {
        $this->price = $price;
        $this->date = $date;
        $this->error = $error;
    }

    static function createPrice(float $price, string $date): DeliveryInfo {
        return new DeliveryInfo($price, $date);
    }

    static function createErrorPrice(string $error): DeliveryInfo {
        return new DeliveryInfo(0.0, '', $error);
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    public function isError(): string
    {
        return !is_null($this->error);
    }
}
