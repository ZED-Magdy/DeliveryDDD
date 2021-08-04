<?php


namespace App\Domain\Repository;


use App\Domain\Model\Client;

interface ClientRepositoryInterface
{
    /**
     * @param string $clientId
     * @return Client|null
     */
    public function findClientById(string $clientId): Client|null;
}