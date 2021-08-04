<?php
declare(strict_types=1);

namespace App\Domain\Model;


use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\CantAcceptMoreThanOneOfferPerOrderException;
use App\Domain\Exception\InvalidEntityOwnerProvidedException;
use App\Domain\Exception\OrderCantBePublished;
use App\Domain\Exception\OrderCantBeUpdatedException;
use Ramsey\Uuid\Uuid;

class Client extends User
{
    /**
     * @var Order[] $orders
     */
    private $orders;

    public static function create(string $email, string $hashedPassword): Client
    {
        return new Client(Uuid::uuid4()->toString(), $email, $hashedPassword);
    }

    /**
     * @throws AccountNotActivatedException
     */
    public function makeOrder(string $id, Place $orderPlace, Place $dropPlace, ?string $note): Order
    {
        $order = new Order($id, $orderPlace, $dropPlace, $this, $note);
        $this->orders[] = $order;
        return $order;
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws OrderCantBeUpdatedException
     */
    public function addProductToOrder(Order $order, string $name, string $qty): Product
    {
        return $order->addProduct($this, $name, $qty);
    }

    /**
     * @throws AccountNotActivatedException
     * @throws OrderCantBePublished
     * @throws InvalidEntityOwnerProvidedException
     */
    public function publishOrder(Order $order)
    {
        if($this->getStatus() !== self::STATUS_ACTIVE)
        {
            throw new AccountNotActivatedException();
        }
        if($this !== $order->getOwner())
        {
            throw new InvalidEntityOwnerProvidedException();
        }
        try {
            $order->markAsPublishedByClient($this);
        } catch (InvalidEntityOwnerProvidedException | OrderCantBePublished $e) {
            throw $e;
        }
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws CantAcceptMoreThanOneOfferPerOrderException
     */
    public function acceptOffer(Offer $offer)
    {
        $offer->getOrder()->markAsProcessing($this, $offer);
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
}