<?php


namespace App\Application\CustomValidator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class UniqueValueInEntityValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $entityRepository = $this->em->getRepository($constraint->entityClass);

        if (!is_scalar($constraint->field)) {
            throw new InvalidArgumentException('"field" parameter should be any scalar type');
        }
        $fieldValue = $value->{$constraint->field}();
        if(method_exists($value, 'get'.ucfirst($constraint->field)))
        {
            $fieldValue = $value->{'get'.ucfirst($constraint->field)}();
        }
        $searchResults = $entityRepository->findBy([
            $constraint->field => $fieldValue
        ]);

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter("{{value}}", $constraint->field)
                ->addViolation();
        }
    }
}