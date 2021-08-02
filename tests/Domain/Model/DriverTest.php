<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\Client;
use App\Domain\Model\Driver;
use App\Domain\Model\Order;
use App\Domain\Model\Place;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DriverTest extends TestCase
{
    public function testCreate()
    {
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertEquals(Driver::STATUS_ACTIVE, $driver->getStatus());
    }

    public function testMakeOffer()
    {
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $client = Client::create("client@example.com", "ey$.aasdadad1223");
        $order = $client->makeOrder(Uuid::uuid4()->toString(), new Place("McDonald", "1231", "1231", "street"),
        new Place("Home", "123131", "12313212", "home"), null);
        $client->addProductToOrder($order, "Big Mac", "2");
        $client->publishOrder($order);
        $driver->makeOffer($order, "100");
        $this->assertCount(1, $driver->getOffers());
    }

    public function testMarkOrderAsArrived()
    {
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $client = Client::create("client@example.com", "ey$.aasdadad1223");
        $order = $client->makeOrder(Uuid::uuid4()->toString(), new Place("McDonald", "1231", "1231", "street"),
            new Place("Home", "123131", "12313212", "home"), null);
        $client->addProductToOrder($order, "Big Mac", "2");
        $client->publishOrder($order);
        $offer = $driver->makeOffer($order, "100");
        $client->acceptOffer($offer);
        $driver->markOrderAsArrived($order);
        $this->assertEquals(Order::STATUS_CONNECTING, $order->getStatus());
    }

    public function testMarkOrderAsDelivered()
    {
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $client = Client::create("client@example.com", "ey$.aasdadad1223");
        $order = $client->makeOrder(Uuid::uuid4()->toString(), new Place("McDonald", "1231", "1231", "street"),
            new Place("Home", "123131", "12313212", "home"), null);
        $client->addProductToOrder($order, "Big Mac", "2");
        $client->publishOrder($order);
        $offer = $driver->makeOffer($order, "100");
        $client->acceptOffer($offer);
        $driver->markOrderAsArrived($order);
        $driver->markOrderAsDelivered($order);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->getStatus());
    }

}
