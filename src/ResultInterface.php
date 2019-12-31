<?php

namespace hanneskod\clean;

interface ResultInterface
{
    /**
     * Check if validation passed
     */
    public function isValid(): bool;

    /**
     * Get validated data
     *
     * @return mixed
     */
    public function getValidData();

    /**
     * Get messages describing validation error
     *
     * @return array<string>
     */
    public function getErrors(): array;
}
