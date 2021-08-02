<?php


namespace App\Domain\Event;


use Symfony\Contracts\EventDispatcher\Event;

class OrderWasPlaced extends Event
{
    private string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }
}