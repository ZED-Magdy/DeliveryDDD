<?php


namespace App\Domain\Event;


use Symfony\Contracts\EventDispatcher\Event;

class OfferWasAccepted extends Event
{
    private string $offerId;

    public function __construct(string $offerId)
    {
        $this->offerId = $offerId;
    }

    /**
     * @return string
     */
    public function getOfferId(): string
    {
        return $this->offerId;
    }
}