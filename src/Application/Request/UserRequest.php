<?php


namespace App\Application\Request;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UserRequest implements RequestDtoInterface
{
    /**
     * @Assert\Email()
     */
    private ?string $email;
    /**
     * @Assert\NotCompromisedPassword()
     */
    private ?string $password;

    public function __construct(null|string $email, null|string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function email(): string|null
    {
        return $this->email;
    }

    public function password(): string|null
    {
        return $this->password;
    }
}