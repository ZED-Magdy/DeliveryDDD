<?php


namespace App\UI\EventSubscriber;


use App\Domain\Event\DomainEventPublisher;
use App\Infrastructure\Persistence\Doctrine\EventSubscribers\UserWasCreatedSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher, private EntityManagerInterface $em)
    {
    }
    public function SubscribeToEvents(RequestEvent $event)
    {
        DomainEventPublisher::instance()->subscribe(new UserWasCreatedSubscriber($this->hasher, $this->em));
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            "kernel.request" => "SubscribeToEvents"
        ];
    }
}