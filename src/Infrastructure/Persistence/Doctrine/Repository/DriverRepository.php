<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;


use App\Domain\Model\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DriverRepository extends ServiceEntityRepository implements \App\Domain\Repository\DriverRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    /**
     * @inheritDoc
     */
    public function findDriverById(string $driverId): Driver|null
    {
        return parent::find($driverId);
    }
}