<?php


namespace App\Domain\Repository;


interface RepositoryInterface
{
    public function add($entity): void;
    public function saveChanges(): void;
}