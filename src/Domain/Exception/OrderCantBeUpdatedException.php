<?php


namespace App\Domain\Exception;


class OrderCantBeUpdatedException extends \Exception
{
    public function __construct()
    {
        parent::__construct("This order cannot be updated since it's not in draft phase");
    }
}