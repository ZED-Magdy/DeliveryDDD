<?php


namespace App\Domain\Event;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventPublisher
{
    private static $instance;
    private function __construct(){}
    private function __clone(): void {}

    public static function getInstance(): EventDispatcherInterface
    {
        if (null === static::$instance)
        {
            static::$instance = new EventDispatcher();
        }
        return static::$instance;
    }
}