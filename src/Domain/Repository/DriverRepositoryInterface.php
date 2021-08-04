<?php


namespace App\Domain\Repository;


use App\Domain\Model\Driver;

interface DriverRepositoryInterface
{
    /**
     * @param string $driverId
     * @return Driver|null
     */
    public function findDriverById(string $driverId): Driver|null;
}