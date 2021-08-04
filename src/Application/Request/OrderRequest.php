<?php


namespace App\Application\Request;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrderRequest implements RequestDtoInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private PlaceDTO $orderPlace;
    /**
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private PlaceDTO $dropPlace;
    private ?string  $note;
    public function __construct(PlaceDTO $orderPlace, PlaceDTO $dropPlace, null|string $note)
    {
        $this->orderPlace = $orderPlace;
        $this->dropPlace = $dropPlace;
        $this->note = $note;
    }
    public function orderPlace()
    {
        return $this->orderPlace;
    }

    public function dropPlace()
    {
        return $this->dropPlace;
    }

    public function note()
    {
        return $this->note;
    }
}