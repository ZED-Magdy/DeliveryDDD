<?php


namespace App\Domain\Model;


use App\Domain\Exception\InvalidEntityOwnerProvidedException;
use App\Domain\Exception\OrderCantBeUpdatedException;
use Ramsey\Uuid\Uuid;

class Product
{
    private string $id;
    private string $name;
    private string $quantity;
    private Order $order;

    /**
     * @throws OrderCantBeUpdatedException
     * @throws InvalidEntityOwnerProvidedException
     */
    public function __construct(string $name, string $quantity, Order $order, Client $client)
    {
        if($order->getStatus() !== Order::STATUS_DRAFT)
        {
            throw new OrderCantBeUpdatedException();
        }
        if($client !== $order->getOwner())
        {
            throw new InvalidEntityOwnerProvidedException("Only order owner can add products to the order");
        }
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->quantity = $quantity;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}