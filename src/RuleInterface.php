<?php

namespace hanneskod\clean;

/**
 * The basic rule iterface
 */
interface RuleInterface
{
    /**
     * Validate tainted data
     *
     * @param  mixed $tainted
     * @return mixed The cleaned data
     * @throws ValidationException If validation fails
     */
    public function validate($tainted);
}
