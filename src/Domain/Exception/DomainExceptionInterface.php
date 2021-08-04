<?php


namespace App\Domain\Exception;


interface DomainExceptionInterface
{
    public function getMessage();

    /**
     * @return int|mixed
     */
    public function getCode();
}