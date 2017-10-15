<?php

namespace hanneskod\clean;

/**
 * Base validator interface
 */
interface ValidatorInterface
{
    /**
     * Validate tainted data
     *
     * @param  mixed $tainted
     * @return mixed The cleaned data
     * @throws Exception If validation fails
     */
    public function validate($tainted);

    /**
     * Trigger validation using validator as a callable
     *
     * @param  mixed $tainted
     * @return mixed The cleaned data
     * @throws Exception If validation fails
     */
    public function __invoke($tainted);
}
