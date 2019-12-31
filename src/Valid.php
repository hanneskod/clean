<?php

declare(strict_types = 1);

namespace hanneskod\clean;

final class Valid implements ResultInterface
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function isValid(): bool
    {
        return true;
    }

    public function getValidData()
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return [];
    }
}
