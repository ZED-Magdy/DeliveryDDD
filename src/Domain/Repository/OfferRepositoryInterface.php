<?php


namespace App\Domain\Repository;


use App\Domain\Model\Offer;
use App\Domain\Model\User;

interface OfferRepositoryInterface
{
    /**
     * @param string $id
     * @return Offer|null
     */
    public function find(string $id): Offer|null;

    /**
     * @param string $orderId
     * @return Offer[]|array
     */
    public function findOrderOffers(string $orderId): array;

}