<?php


namespace App\Domain\Event;


use DateTimeImmutable;

class UserWasCreated implements DomainEvent
{
    private string $id;
    private string $email;
    private string $password;
    private string $userType;


    public function __construct(string $id, string $email, string $password, string $userType)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->userType = $userType;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}