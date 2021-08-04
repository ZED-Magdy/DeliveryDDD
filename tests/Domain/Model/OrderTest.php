<?php

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidActionForCurrentOrderState;
use App\Domain\Exception\OrderCantBePublished;
use App\Domain\Model\Client;
use App\Domain\Model\Driver;
use App\Domain\Model\Order;
use App\Domain\Model\Place;
use App\Domain\Model\Product;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class OrderTest extends TestCase
{
    public function testCreate()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $this->assertInstanceOf(Order::class, $order);
    }
    public function testAddProduct()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $product = $order->addProduct($client, "new product", "5");
        $this->assertInstanceOf(Product::class, $product);
        $this->assertCount(1, $order->getProducts());
    }

    public function testMarkAsPublishedByClient()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $product = $order->addProduct($client, "new product", "5");
        $order->markAsPublishedByClient($client);

        $this->assertEquals(Order::STATUS_PENDING, $order->getStatus());
        $this->assertNotNull($order->getPublishedAt());
    }

    public function testClientCantPublishOrderWithNoProducts()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");

        $this->expectException(OrderCantBePublished::class);

        $order->markAsPublishedByClient($client);

        $this->assertEquals(Order::STATUS_DRAFT, $order->getStatus());
        $this->assertNull($order->getPublishedAt());
    }

    public function testMarkAsProcessing()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $order->addProduct($client, "new product", "5");
        $order->markAsPublishedByClient($client);
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $offer = $driver->makeOffer($order, "55");
        $order->markAsProcessing($client, $offer);
        $this->assertEquals(Order::STATUS_PROCESSING, $order->getStatus());
        $this->assertNotNull($order->getOfferAcceptedAt());
    }

    public function testDriverCantMakeOfferOnOrderInDraftState()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");

        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $this->expectException(InvalidActionForCurrentOrderState::class);
        $driver->makeOffer($order, "55");
    }

    public function testMarkAsConnecting()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $order->addProduct($client, "new product", "5");
        $order->markAsPublishedByClient($client);
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $offer = $driver->makeOffer($order, "55");
        $order->markAsProcessing($client, $offer);
        $order->markAsConnecting($driver);
        $this->assertEquals(Order::STATUS_CONNECTING, $order->getStatus());
        $this->assertNotNull($order->getDriverArrivedAt());
    }

    public function testMarkAsDelivered()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $product = $order->addProduct($client, "new product", "5");
        $order->markAsPublishedByClient($client);
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $offer = $driver->makeOffer($order, "55");
        $order->markAsProcessing($client, $offer);
        $order->markAsConnecting($driver);
        $order->markAsDelivered($driver);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->getStatus());
        $this->assertNotNull($order->getFinishedAt());
    }

    public function testMarkAsFailed()
    {
        $client = Client::create("client@example.com", "ey$.sadasd123asd123");
        $order = new Order(Uuid::uuid4()->toString(),
            new Place("McDonald","123.123","321.321","McDonald st"),
            new Place("Home","321.321","123.123","Home st"),
            $client,"");
        $product = $order->addProduct($client, "new product", "5");
        $order->markAsPublishedByClient($client);
        $driver = Driver::create("driver@example.com", "ey$.2312313");
        $offer = $driver->makeOffer($order, "55");
        $order->markAsProcessing($client, $offer);
        $order->markAsConnecting($driver);
        //Should wait 10 minutes from arrival time before mark as failed
        $this->expectException(InvalidActionForCurrentOrderState::class);
        $order->markAsFailed($driver);
    }
}
