<?php


namespace App\Application\Request;

class PlaceDTO
{
    private ?string $name;
    private ?string $longitude;
    private ?string $latitude;
    private ?string $address;

    public function __construct(null|string $name, null|string $longitude, null|string $latitude, null|string $address)
    {
        $this->name = $name;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->address = $address;
    }

    public function name()
    {
        return $this->name;
    }

    public function longitude()
    {
        return $this->longitude;
    }

    public function latitude()
    {
        return $this->latitude;
    }

    public function address()
    {
        return $this->address;
    }
}