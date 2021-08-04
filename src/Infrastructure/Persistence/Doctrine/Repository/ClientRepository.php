<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;


use App\Domain\Model\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findClientById(string $clientId): Client|null
    {
        return parent::find($clientId);
    }
}