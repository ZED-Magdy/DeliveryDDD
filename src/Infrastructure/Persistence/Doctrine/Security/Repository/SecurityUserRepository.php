<?php


namespace App\Infrastructure\Persistence\Doctrine\Security\Repository;


use App\Infrastructure\Security\SecurityUserRepositoryInterface;
use App\Infrastructure\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SecurityUserRepository extends ServiceEntityRepository implements SecurityUserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['email' => $username]);
    }
}