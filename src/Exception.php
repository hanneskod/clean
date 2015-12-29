<?php

namespace hanneskod\clean;

/**
 * Thrown when validation fails
 */
class Exception extends \Exception
{
    /**
     * @var string[] Name(s) of failing rule(s)
     */
    private $ruleNames = [];

    /**
     * Push name of failing rule
     *
     * @param  string $name
     * @return void
     */
    public function pushRuleName($name)
    {
        $this->ruleNames[] = $name;
    }

    /**
     * Get name of failing rule
     *
     * @return string
     */
    public function getSourceRuleName()
    {
        return implode(array_reverse($this->ruleNames), '::');
    }
}
