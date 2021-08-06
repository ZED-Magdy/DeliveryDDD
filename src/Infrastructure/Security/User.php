<?php


namespace App\Infrastructure\Security;


use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    private string $id;
    private string $email;
    private string $password;
    private array  $roles;

    public function __construct(string $id, string $email, array $roles)
    {

        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
    }
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array_unique($this->roles);
    }

    public function setHashedPassword(string $hashedPassword)
    {
        $this->password= $hashedPassword;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->password = "";
    }

    /**
     * @return string
     * @deprecated
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->getUserIdentifier() == $user->getUserIdentifier();
    }
}