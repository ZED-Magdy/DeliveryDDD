<?php
declare(strict_types=1);

namespace App\Domain\Model;


use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\UserWasCreated;

abstract class User
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_BANNED = 2;

    private string $id;
    private string $email;
    private string $password;
    protected int $status;

    protected function __construct(string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->status = self::STATUS_ACTIVE;
        DomainEventPublisher::instance()->publish(new UserWasCreated($id, $email, $password, get_called_class()));
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function updatePassword(string $password)
    {
        $this->password = $password;
    }
}