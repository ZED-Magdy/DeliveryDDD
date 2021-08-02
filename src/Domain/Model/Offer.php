<?php


namespace App\Domain\Model;


use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\CantMakeOffersUntilDeliverOtherOrders;
use App\Domain\Exception\InvalidActionForCurrentOrderState;
use Decimal\Decimal;

class Offer
{
    private Decimal $price;
    private Driver $driver;
    private Order $order;

    /**
     * Offer constructor.
     * @param Driver $driver
     * @param Order $order
     * @param string $price
     * @throws AccountNotActivatedException
     * @throws InvalidActionForCurrentOrderState
     * @throws CantMakeOffersUntilDeliverOtherOrders
     */
    public function __construct(Driver $driver, Order $order, string $price)
    {
        if($driver->getStatus() !== Driver::STATUS_ACTIVE)
        {
            throw new AccountNotActivatedException();
        }
        if($order->getStatus() !== Order::STATUS_PENDING)
        {
            throw new InvalidActionForCurrentOrderState("You can only make offers on orders in pending status");
        }
        foreach ($driver->getOrders() as $driverOrder)
        {
            if($driverOrder->getStatus() !== Order::STATUS_DELIVERED || $driverOrder->getStatus() !== Order::STATUS_FAILED)
            {
                throw new CantMakeOffersUntilDeliverOtherOrders("You cant make any new offers until you finish all other orders");
            }
        }
        $this->driver = $driver;
        $this->order = $order;
        $this->price = new Decimal($price, 10);
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price->toString();
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}