<?php

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
     *
     * @param  string $name
     * @return void
     */
    public function pushValidatorName($name)
    {
        $this->validatorNames[] = $name;
    }

    /**
     * Get name of failing validator
     *
     * @return string
     */
    public function getSourceValidatorName()
    {
        return implode(array_reverse($this->validatorNames), '::');
    }
}
