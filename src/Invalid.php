<?php

declare(strict_types = 1);

namespace hanneskod\clean;

final class Invalid implements ResultInterface
{
    private \Throwable $error;

    public function __construct(\Throwable $error)
    {
        $this->error = $error;
    }

    public function isValid(): bool
    {
        return false;
    }

    public function getValidData()
    {
        throw $this->error;
    }

    public function getErrors(): array
    {
        return [$this->error->getMessage()];
    }
}
