<?php


namespace App\Application\Transformers;


use App\Application\Response\UserResponse;
use App\Domain\Model\Client;
use App\Domain\Model\Driver;
use App\Domain\Model\User;
use Symfony\Component\Form\DataTransformerInterface;

class UserTransformer implements DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if($value instanceof User)
        {
            return $this->transformOne($value);
        }
        if(is_array($value))
        {
            $data = [];
            foreach ($value as $user)
            {
                array_push($data, $this->transformOne($user));
            }
            return $data;
        }
    }

    private function transformOne(User $user)
    {
        $type = null;
        if($user instanceof Client)
        {
            $type = "CLIENT";
        }
        if($user instanceof Driver)
        {
            $type = "DRIVER";
        }
        return new UserResponse($user->getId(), $user->getEmail(), $user->getStatus(), $type);
    }
    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        // TODO: Implement reverseTransform() method.
    }
}