<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;


use App\Domain\Model\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveChanges(): void
    {
        $this->_em->flush();
    }
    public function add($entity): void
    {
        $this->_em->persist($entity);
    }
}