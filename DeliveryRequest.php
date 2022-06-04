<?php

/** Request for information about delivery price */
class DeliveryRequest
{
    /** @var string Where we are shipping from */
    public $sourceKladr;
    /** @var string Where we are shipping to */
    public $targetKladr;
    /** @var float Shipment weight in kg */
    public $weight;

    public function __construct(string $sourceKladr, string $targetKladr, float $weight)
    {
        $this->sourceKladr = $sourceKladr;
        $this->targetKladr = $targetKladr;
        $this->weight = $weight;
    }
}
