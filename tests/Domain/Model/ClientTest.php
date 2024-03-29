<?php

namespace App\Tests\Domain\Model;

use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\InvalidEntityOwnerProvidedException;
use App\Domain\Model\Client;
use App\Domain\Model\Order;
use App\Domain\Model\Place;
use App\Domain\Model\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function Symfony\Component\String\u;

class ClientTest extends TestCase
{

    public function testCreate()
    {
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.sadasd123asd123");
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals(User::STATUS_ACTIVE, $client->getStatus());
    }

    public function testClientCanCreateOrder()
    {
        $client = Client::create(Uuid::uuid4()->toString(),"client@example.com", "ey$.sadasd123asd123");
        try {
            $order = $client->makeOrder(
                Uuid::uuid4()->toString(),
                new Place("McDonald", "123.123", "321.321", "McDonald st"),
                new Place("Home", "321.321", "123.123", "Home st"),
                ""
            );
            $this->assertCount(1, $client->getOrders());
        } catch (AccountNotActivatedException $e) {
        }
    }

    public function testClientCanAddProductToOrder()
    {
        $client = Client::create(Uuid::uuid4()->toString(),"client@example.com", "ey$.sadasd123asd123");
        try {
            $order = $client->makeOrder(
                Uuid::uuid4()->toString(),
                new Place("McDonald", "123.123", "321.321", "McDonald st"),
                new Place("Home", "321.321", "123.123", "Home st"),
                ""
            );
            $client->addProductToOrder($order, "Cheese burger", "1");
            $this->assertCount(1, $order->getProducts());
        } catch (AccountNotActivatedException $e) {
        }
    }

    public function testClientsCantAddProductToOtherClientsOrder()
    {
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.sadasd123asd123");
        $client2 = Client::create(Uuid::uuid4()->toString(), "client2@example.com", "ey$.sadasd123asd123");
        $order = $client->makeOrder(
            Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            ""
        );
        $this->expectException(InvalidEntityOwnerProvidedException::class);
        $client2->addProductToOrder($order, "Cheese burger", "1");
        $this->assertCount(0, $order->getProducts());
    }

    public function testClientCanPublishOrder()
    {
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.sadasd123asd123");
        $order = $client->makeOrder(
            Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            ""
        );
        $client->addProductToOrder($order, "Cheese burger", "1");
        $client->publishOrder($order);
        $this->assertEquals(Order::STATUS_PENDING, $order->getStatus());
    }
    public function testClientsCantPublishOtherClientsOrder()
    {
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.sadasd123asd123");
        $client2 = Client::create(Uuid::uuid4()->toString(), "client2@example.com", "ey$.sadasd123asd123");
        $order = $client->makeOrder(
            Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            ""
        );
        $client->addProductToOrder($order, "Cheese burger", "1");
        $this->expectException(InvalidEntityOwnerProvidedException::class);
        $client2->publishOrder($order);
        $this->assertEquals(Order::STATUS_DRAFT, $order->getStatus());
    }

}
