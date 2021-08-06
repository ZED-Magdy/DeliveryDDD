<?php


namespace App\Infrastructure\Security;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthUserProvider implements UserProviderInterface
{
    private SecurityUserRepositoryInterface $repository;

    public function __construct(SecurityUserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        $username = $user->getUserIdentifier();

        return $this->fetchUser($username);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->fetchUser($identifier);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username): ?UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    private function fetchUser(string $username): ?UserInterface
    {
        return $this->repository->findByUsername($username);
    }
}