<?php


namespace App\Application\Request;

use App\Domain\Model\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\CustomValidator\UniqueValueInEntity;

#[UniqueValueInEntity(field: "email", entityClass: User::class)]
class UserRequest implements RequestDtoInterface
{
    /**
     * @Assert\Email()
     */
    private ?string $email;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
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