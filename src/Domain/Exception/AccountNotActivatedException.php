<?php


namespace App\Domain\Exception;


use Throwable;

class AccountNotActivatedException extends \Exception implements DomainExceptionInterface
{
    public function __construct()
    {
        parent::__construct("You cant do this action since your account status is not active");
    }
}