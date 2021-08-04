<?php


namespace App\Application\Response;


class UserResponse
{
    private string $id;
    private ?string $email;
    private ?int $status;
    private ?string $type;

    public function __construct(string $id, string $email, int $status, string $type)
    {
        $this->email = $email;
        $this->status = $status;
        $this->type = $type;
        $this->id = $id;
    }
    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function getStatus(): int|null
    {
        return $this->status;
    }

    public function getType(): string|null
    {
        return $this->type;
    }
}