<?php


namespace App\Domain\Model;


use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\CantMakeOffersUntilDeliverOtherOrders;
use App\Domain\Exception\InvalidActionForCurrentOrderState;
use App\Domain\Exception\InvalidEntityOwnerProvidedException;

class Offer
{
    private string $id;
    private string $price;
    private Driver $driver;
    private Order $order;
    private ?\DateTimeImmutable $acceptedAt;

    /**
     * Offer constructor.
     * @param string $id
     * @param Driver $driver
     * @param Order $order
     * @param string $price
     * @throws AccountNotActivatedException
     * @throws CantMakeOffersUntilDeliverOtherOrders
     * @throws InvalidActionForCurrentOrderState
     */
    public function __construct(string $id, Driver $driver, Order $order, string $price)
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
        $this->price = $price;
        $this->id = $id;
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     */
    public function markAsAccepted(Client $client)
    {
        if($client !== $this->getOrder()->getOwner())
        {
            throw new InvalidEntityOwnerProvidedException();
        }
        $this->acceptedAt = new \DateTimeImmutable('now');
        $this->getDriver()->notifyOfferAccepted($this);
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }
}