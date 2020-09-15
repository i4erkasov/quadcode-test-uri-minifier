<?php

namespace App\Extensions;

use App\Exceptions\AppInvalidParametersException;
use App\Interfaces\RequestDtoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class RequestDtoResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    /**
     * RequestDTOResolver constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $reflection = new \ReflectionClass($argument->getType());

        if ($reflection->implementsInterface(RequestDtoInterface::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @throws \Exception
     *
     * @return \Generator|iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class  = $argument->getType();
        $dto    = new $class($request);
        $errors = $this->validator->validate(new $class($request));

        if ($errors->count()) {
            throw new AppInvalidParametersException($errors->get(0)->getMessage());
        }

        yield $dto;
    }
}
