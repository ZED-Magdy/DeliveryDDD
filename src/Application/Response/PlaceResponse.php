<?php


namespace App\Application\Response;


class PlaceResponse
{
    private ?string $name;
    private ?string $longitude;
    private ?string $latitude;
    private ?string $address;

    public function __construct(?string $name, ?string $longitude, ?string $latitude, ?string $address)
    {
        $this->name = $name;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }
}