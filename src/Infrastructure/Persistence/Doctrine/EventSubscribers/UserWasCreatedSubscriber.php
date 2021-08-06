<?php


namespace App\Infrastructure\Persistence\Doctrine\EventSubscribers;


use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;
use App\Domain\Event\UserWasCreated;
use App\Domain\Model\Driver;
use App\Infrastructure\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserWasCreatedSubscriber implements DomainEventSubscriber
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $em)
    {
        $this->hasher = $hasher;
        $this->em = $em;
    }

    public function handle(DomainEvent|UserWasCreated $event)
    {
        $role = ["ROLE_CLIENT"];
        if($event->getUserType() instanceof Driver){
            $role = ["ROLE_DRIVER"];
        }
        $securityUser = new User(Uuid::uuid4()->toString(), $event->getEmail(), $role);
        $securityUser->setHashedPassword($this->hasher->hashPassword($securityUser, $event->getPassword()));
        $this->em->persist($securityUser);
        $this->em->flush();
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        return $aDomainEvent instanceof UserWasCreated;
    }
}