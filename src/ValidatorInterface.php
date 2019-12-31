<?php

namespace hanneskod\clean;

interface ValidatorInterface
{
    /**
     * Apply validator to data
     *
     * @param mixed $data
     */
    public function applyTo($data): ResultInterface;

    /**
     * Validate tainted data
     *
     * @param mixed $data
     * @return mixed The cleaned data
     * @throws Exception If validation fails
     */
    public function validate($data);
}
