<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Extension point for custom validators
 */
abstract class AbstractValidator implements ValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = $this->create();
    }

    abstract protected function create(): ValidatorInterface;

    public function applyTo($data): ResultInterface
    {
        return $this->validator->applyTo($data);
    }

    public function validate($data)
    {
        return $this->applyTo($data)->getValidData();
    }
}
