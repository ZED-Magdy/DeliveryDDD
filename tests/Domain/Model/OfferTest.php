<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\Client;
use App\Domain\Model\Driver;
use App\Domain\Model\Offer;
use App\Domain\Model\Place;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class OfferTest extends TestCase
{

    public function testCreate()
    {
        $driver = Driver::create(Uuid::uuid4()->toString(), "driver@example.com", "ey$.2312313");
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.aasdadad1223");
        $order = $client->makeOrder(Uuid::uuid4()->toString(), new Place("McDonald", "1231", "1231", "street"),
            new Place("Home", "123131", "12313212", "home"), null);
        $client->addProductToOrder($order, "Big Mac", "2");
        $client->publishOrder($order);
        $offer = new Offer(Uuid::uuid4()->toString(), $driver, $order, "50");
        $this->assertInstanceOf(Offer::class, $offer);
    }

    public function testMarkAsAccepted()
    {
        $driver = Driver::create(Uuid::uuid4()->toString(), "driver@example.com", "ey$.2312313");
        $client = Client::create(Uuid::uuid4()->toString(), "client@example.com", "ey$.aasdadad1223");
        $order = $client->makeOrder(Uuid::uuid4()->toString(), new Place("McDonald", "1231", "1231", "street"),
            new Place("Home", "123131", "12313212", "home"), null);
        $client->addProductToOrder($order, "Big Mac", "2");
        $client->publishOrder($order);
        $offer = new Offer(Uuid::uuid4()->toString(), $driver, $order, "50");
        $offer->markAsAccepted($client);
        $this->assertNotNull($offer->getAcceptedAt());
    }
}
