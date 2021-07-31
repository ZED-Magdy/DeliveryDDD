<?php
declare(strict_types=1);

namespace App\Domain\Model;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Client extends User
{
    /**
     * @var Order[] $orders
     */
    private array $orders;

    public static function create(string $id, string $email, string $hashedPassword): Client
    {
        return new Client($id, $email, $hashedPassword);
    }

    public function makeOrder(string $id, Place $orderPlace, Place $dropPlace, ?string $note): Order
    {
        $order = new Order($id, $orderPlace, $dropPlace, $this, $note);
        $this->orders[] = $order;
        return $order;
    }

    public function addProductToOrder(Order $order, string $name, string $qty): Product
    {
        return $order->addProduct($this, $name, $qty);
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
}