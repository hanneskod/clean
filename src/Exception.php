<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Thrown when validation fails
 */
class Exception extends \Exception
{
    /**
     * @var string[] Name(s) of failing validator(s)
     */
    private $validatorNames = [];

    /**
     * Push name of failing validator
     */
    public function pushValidatorName(string $name): void
    {
        $this->validatorNames[] = $name;
    }

    /**
     * Get name of failing validator
     */
    public function getSourceValidatorName(): string
    {
        return implode('::', array_reverse($this->validatorNames));
    }
}
