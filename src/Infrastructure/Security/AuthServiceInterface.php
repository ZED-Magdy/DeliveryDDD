<?php


namespace App\Infrastructure\Security;


use App\Domain\Model\Client;
use App\Domain\Model\Driver;

interface AuthServiceInterface
{
    public function getAuthUser(): null|Client|Driver;
}