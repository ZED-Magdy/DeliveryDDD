<?php


namespace App\Infrastructure\Security;


use Symfony\Component\Security\Core\User\UserInterface;

interface SecurityUserRepositoryInterface
{
    public function findByUsername(string $username): ?UserInterface;
}