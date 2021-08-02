<?php
declare(strict_types=1);

namespace App\Domain\Model;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Order
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_CONNECTING = 3;
    public const STATUS_DELIVERED = 4;

    private string $id;
    private Place $orderPlace;
    private Place $dropPlace;
    private Client $owner;
    private ?string $note;
    private int $status;
    private \DateTimeImmutable $publishedAt;
    /**
     * @var Product[] $products
     */
    private array $products;

    public function __construct(string $id, Place $orderPlace, Place $dropPlace, Client $owner, ?string $note)
    {
        if($owner->getStatus() !== User::STATUS_ACTIVE)
        {
            throw new UnprocessableEntityHttpException("Only activated clients can make orders");
        }
        $this->id = $id;
        $this->orderPlace = $orderPlace;
        $this->dropPlace = $dropPlace;
        $this->owner = $owner;
        $this->note = $note;
        $this->status = self::STATUS_DRAFT;
    }

    public function addProduct(Client $client, string $name, string $quantity): Product
    {
        $product = new Product($name, $quantity, $this, $client);
        $this->products[] = $product;
        return $product;
    }

    public function markAsPublishedByClient(Client $client)
    {
        if($this->getOwner() !== $client)
        {
            throw new UnprocessableEntityHttpException("this client doesnt have the permission to publish this order");
        }
        if(count($this->getProducts()) < 1)
        {
            throw new UnprocessableEntityHttpException("Order must have at least 1 product to be published");
        }

        $this->status = self::STATUS_PENDING;
        $this->publishedAt = new \DateTimeImmutable("now");
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
}