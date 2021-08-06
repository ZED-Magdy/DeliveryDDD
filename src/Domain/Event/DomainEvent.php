<?php


namespace App\Domain\Event;


use DateTimeImmutable;

interface DomainEvent
{
    /**
     * @return DateTimeImmutable
     */
    public function occurredOn(): DateTimeImmutable;
}