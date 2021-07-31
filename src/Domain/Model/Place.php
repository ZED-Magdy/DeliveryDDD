<?php
declare(strict_types=1);

namespace App\Domain\Model;


class Place
{
    private string $name;
    private string $longitude;
    private string $latitude;
    private string $address;

    public function __construct(string $name, string $longitude, string $latitude, string $address)
    {
        $this->name = $name;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->address = $address;
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
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}