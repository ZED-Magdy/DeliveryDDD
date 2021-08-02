<?php


namespace App\Domain\Model;

use Decimal\Decimal;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Driver extends User
{
    private array $offers;
    /**
     * @var Order[] $orders
     */
    private array $orders;
    private Decimal $fees;

    public static function create(string $id, string $email, string $hashedPassword): Driver
    {
        return new Driver($id, $email, $hashedPassword);
    }

    public function makeOffer(Order $order, string $price): Offer
    {
        $offer = new Offer($this, $order, $price);
        $this->offers[] = $offer;
        return $offer;
    }

    public function markOrderAsArrived(Order $order)
    {
        $order->markAsConnecting($this);
    }

    public function markOrderAsFailed(Order $order)
    {
        $order->markAsFailed($this);
    }

    public function markOrderAsDelivered(Order $order)
    {
        $order->markAsDelivered($this);
    }

    public function addFeesForTheOrder(Order $order)
    {
        if($order->getDriver() !== $this)
        {
            throw new UnprocessableEntityHttpException("The order driver is not this driver");
        }
        if($order->getStatus() !== Order::STATUS_DELIVERED)
        {
            throw new UnprocessableEntityHttpException("You cant add fees to the order until it has been delivered");
        }
        $this->fees += ($order->getPrice() * 15) / 100;
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getFees(): string
    {
        return $this->fees->toString();
    }
}