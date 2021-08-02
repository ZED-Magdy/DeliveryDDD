<?php
declare(strict_types=1);

namespace App\Domain\Model;


use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Exception\CantAcceptMoreThanOneOfferPerOrderException;
use App\Domain\Exception\InvalidActionForCurrentOrderState;
use App\Domain\Exception\InvalidEntityOwnerProvidedException;
use App\Domain\Exception\OrderCantBePublished;
use App\Domain\Exception\OrderCantBeUpdatedException;
use DateTimeImmutable;
use Decimal\Decimal;

class Order
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_CONNECTING = 3;
    public const STATUS_DELIVERED = 4;
    public const STATUS_FAILED = 5;

    private string $id;
    private Place $orderPlace;
    private Place $dropPlace;
    private Client $owner;
    private ?Driver $driver;
    private ?Decimal $price;
    private ?Offer $acceptedOffer;
    private ?string $note;
    private int $status;
    private ?DateTimeImmutable $publishedAt;
    private ?DateTimeImmutable $offerAcceptedAt;
    private ?DateTimeImmutable $driverArrivedAt;
    private ?DateTimeImmutable $finishedAt;
    /**
     * @var Product[] $products
     */
    private array $products;

    /**
     * @throws AccountNotActivatedException
     */
    public function __construct(string $id, Place $orderPlace, Place $dropPlace, Client $owner, ?string $note)
    {
        if($owner->getStatus() !== User::STATUS_ACTIVE)
        {
            throw new AccountNotActivatedException();
        }
        $this->id = $id;
        $this->orderPlace = $orderPlace;
        $this->dropPlace = $dropPlace;
        $this->owner = $owner;
        $this->note = $note;
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @throws OrderCantBeUpdatedException
     */
    public function addProduct(Client $client, string $name, string $quantity): Product
    {
        $product = new Product($name, $quantity, $this, $client);
        $this->products[] = $product;
        return $product;
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws OrderCantBePublished
     */
    public function markAsPublishedByClient(Client $client): void
    {
        if($this->getOwner() !== $client)
        {
            throw new InvalidEntityOwnerProvidedException("this client doesnt have the permission to publish this order");
        }
        if(count($this->getProducts()) < 1)
        {
            throw new OrderCantBePublished("Order must have at least 1 product to be published");
        }

        $this->status = self::STATUS_PENDING;
        $this->publishedAt = new DateTimeImmutable("now");
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws CantAcceptMoreThanOneOfferPerOrderException
     */
    public function markAsProcessing(Client $client, Offer $offer): void
    {
        if($client !== $this->getOwner())
        {
            throw new InvalidEntityOwnerProvidedException("You cant accept offers for others orders");
        }
        if($this->getStatus() !== Order::STATUS_PENDING)
        {
            throw new CantAcceptMoreThanOneOfferPerOrderException("You cant accept more than one offer on the order");
        }
        $this->setPrice($offer->getPrice());
        $this->driver = $offer->getDriver();
        $this->acceptedOffer = $offer;
        $this->offerAcceptedAt = new DateTimeImmutable('now');
        $this->status = self::STATUS_PROCESSING;
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markAsConnecting(Driver $driver)
    {
        if($driver !== $this->getDriver())
        {
            throw new InvalidEntityOwnerProvidedException("You dont have the permission to do this action");
        }
        if($this->getStatus() !== self::STATUS_PROCESSING)
        {
            throw new InvalidActionForCurrentOrderState("You cant mark order as arrived when it's not in processing state");
        }
        $this->status = self::STATUS_CONNECTING;
        $this->driverArrivedAt = new DateTimeImmutable('now');
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markAsFailed(Driver $driver)
    {
        if($driver !== $this->getDriver())
        {
            throw new InvalidEntityOwnerProvidedException("You dont have the permission to do this action");
        }
        if($this->getStatus() !== self::STATUS_CONNECTING)
        {
            throw new InvalidActionForCurrentOrderState("You cant mark order as failed when it's not in connecting state");
        }
        if($this->getDriverArrivedAt()->add(new \DateInterval("PT10M")) > new DateTimeImmutable('now'))
        {
            throw new InvalidActionForCurrentOrderState("you cant mark order as failed until it has been 10 minutes from the arrival time");
        }
        $this->status = self::STATUS_FAILED;
        $this->finishedAt = new DateTimeImmutable('now');
    }

    /**
     * @throws InvalidEntityOwnerProvidedException
     * @throws InvalidActionForCurrentOrderState
     */
    public function markAsDelivered(Driver $driver)
    {
        if($this->getDriver() !== $driver)
        {
            throw new InvalidEntityOwnerProvidedException("You dont have the permission to do this action");
        }
        if($this->getStatus() !== self::STATUS_CONNECTING)
        {
            throw new InvalidActionForCurrentOrderState("You cant mark order as delivered when it's not in connecting state");
        }
        $this->status = self::STATUS_DELIVERED;
        $this->finishedAt = new DateTimeImmutable('now');
        $driver->addFeesForTheOrder($this);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Place
     */
    public function getOrderPlace(): Place
    {
        return $this->orderPlace;
    }

    /**
     * @return Place
     */
    public function getDropPlace(): Place
    {
        return $this->dropPlace;
    }

    /**
     * @return Client
     */
    public function getOwner(): Client
    {
        return $this->owner;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price->toFloat();
    }
    private function setPrice(string $price)
    {
        $this->price = new Decimal($price, 10);
    }
    /**
     * @return Offer|null
     */
    public function getAcceptedOffer(): ?Offer
    {
        return $this->acceptedOffer;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getOfferAcceptedAt(): ?DateTimeImmutable
    {
        return $this->offerAcceptedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDriverArrivedAt(): ?DateTimeImmutable
    {
        return $this->driverArrivedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }
}