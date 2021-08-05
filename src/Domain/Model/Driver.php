<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\OfferWasAccepted;
use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\CantMakeOffersUntilDeliverOtherOrders;
use App\Domain\Exception\InvalidActionForCurrentOrderState;
use App\Domain\Exception\InvalidEntityOwnerProvidedException;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

class Driver extends User
{
    /**
     * @var Offer[] $offers
     */
    private $offers;
    /**
     * @var Order[] $orders
     */
    private $orders;
    private string $fees = "0.0";

    public static function create(string $email, string $hashedPassword): Driver
    {
        $driver = new Driver(Uuid::uuid4()->toString(), $email, $hashedPassword);
        $driver->orders = new ArrayCollection();
        $driver->offers = new ArrayCollection();
        return $driver;
    }

    /**
     * @throws AccountNotActivatedException
     * @throws CantMakeOffersUntilDeliverOtherOrders
     * @throws InvalidActionForCurrentOrderState
     */
    public function makeOffer(Order $order, string $price): Offer
    {
        $offer = new Offer(Uuid::uuid4()->toString(), $this, $order, $price);
        $this->offers[] = $offer;
        return $offer;
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markOrderAsArrived(Order $order)
    {
        $order->markAsConnecting($this);
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markOrderAsFailed(Order $order)
    {
        $order->markAsFailed($this);
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markOrderAsDelivered(Order $order)
    {
        $order->markAsDelivered($this);
    }
    /**
     * @throws InvalidEntityOwnerProvidedException
     */
    public function notifyOfferAccepted(Offer $offer)
    {
        if($offer->getDriver() !== $this)
        {
            throw new InvalidEntityOwnerProvidedException();
        }
        $this->orders[] = $offer->getOrder();
        DomainEventPublisher::getInstance()->dispatch(new OfferWasAccepted($offer->getId()));
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function addFeesForTheOrder(Order $order)
    {
        if($order->getDriver() !== $this)
        {
            throw new InvalidEntityOwnerProvidedException("The order driver is not this driver");
        }
        if($order->getStatus() !== Order::STATUS_DELIVERED)
        {
            throw new InvalidActionForCurrentOrderState("You cant add fees to the order until it has been delivered");
        }
        $orderFees = (((float)$order->getPrice() * 15) / 100);
        $totalFees = (float)$this->getFees() + $orderFees;
        $this->fees = (string)$totalFees;
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
        return $this->fees;
    }

    public function getOffers(): array
    {
        return $this->offers;
    }
}