<?php


namespace App\Application\Request;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if(!class_exists($argument->getType())){
            return false;
        }
        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(RequestDtoInterface::class)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        if (str_starts_with($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
        // creating new instance of custom request DTO
        $class = $argument->getType();
        $reflec = new \ReflectionClass($class);
        $args = [];
        foreach ($reflec->getProperties() as $property)
        {
            $arg = null;
            if(class_exists($property->getType()))
            {
                $innerArgs = $request->request->get($property->getName());
                $arg = (new \ReflectionClass($property->getType()->getName()))->newInstanceArgs($innerArgs);
            }else{
                $arg = $request->request->get($property->getName());
            }
            array_push($args, $arg);
        }
        $dto = $reflec->newInstanceArgs($args);

        yield $dto;
    }
}