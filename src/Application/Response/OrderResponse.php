<?php


namespace App\Application\Response;


class OrderResponse
{
    private string $id;
    private PlaceResponse $orderPlace;
    private PlaceResponse $dropPlace;
    private ?string $note;
    private string $status;
    private ?string $price;
    private ?string $publishedAt;
    private ?string $offerAcceptedAt;
    private ?string $driverArrivedAt;
    private ?string $finishedAt;
    public function __construct(string $id, PlaceResponse $orderPlace, PlaceResponse $dropPlace, null|string $note, string $status, null|string $price, null|string $publishedAt, null|string $offerAcceptedAt, null|string $driverArrivedAt, null|string $finishedAt)
    {
        $this->id = $id;
        $this->orderPlace = $orderPlace;
        $this->dropPlace = $dropPlace;
        $this->note = $note;
        $this->status = $status;
        $this->price = $price;
        $this->publishedAt = $publishedAt;
        $this->offerAcceptedAt = $offerAcceptedAt;
        $this->driverArrivedAt = $driverArrivedAt;
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return PlaceResponse
     */
    public function getOrderPlace(): PlaceResponse
    {
        return $this->orderPlace;
    }

    /**
     * @return PlaceResponse
     */
    public function getDropPlace(): PlaceResponse
    {
        return $this->dropPlace;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @return string|null
     */
    public function getPublishedAt(): ?string
    {
        return $this->publishedAt;
    }

    /**
     * @return string|null
     */
    public function getOfferAcceptedAt(): ?string
    {
        return $this->offerAcceptedAt;
    }

    /**
     * @return string|null
     */
    public function getDriverArrivedAt(): ?string
    {
        return $this->driverArrivedAt;
    }

    /**
     * @return string|null
     */
    public function getFinishedAt(): ?string
    {
        return $this->finishedAt;
    }
}