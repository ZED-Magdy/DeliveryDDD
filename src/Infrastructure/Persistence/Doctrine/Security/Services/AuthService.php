<?php


namespace App\Infrastructure\Persistence\Doctrine\Security\Services;


use App\Domain\Model\Client;
use App\Domain\Model\Driver;
use App\Domain\Model\User;
use App\Infrastructure\Security\AuthServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AuthService implements AuthServiceInterface
{
    public function __construct(private Security $security, private EntityManagerInterface $em)
    {
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getAuthUser(): null|Client|Driver
    {
        if($this->security->getUser() == null)
        {
            return null;
        }

        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = ?1')
            ->setParameter(1, $this->security->getUser()->getId())
            ->getQuery()
            ->getSingleResult();
    }
}