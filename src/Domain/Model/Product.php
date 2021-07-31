<?php


namespace App\Domain\Model;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Product
{
    private string $name;
    private string $quantity;
    private Order $order;

    public function __construct(string $name, string $quantity, Order $order, Client $client)
    {
        if($order->getStatus() !== Order::STATUS_DRAFT)
        {
            throw new UnprocessableEntityHttpException("You cant add products to order after it has been published");
        }
        if($client !== $order->getOwner())
        {
            throw new UnprocessableEntityHttpException("Only order owner can add products to the order");
        }
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