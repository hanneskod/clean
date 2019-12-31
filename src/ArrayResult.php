<?php

declare(strict_types = 1);

namespace hanneskod\clean;

final class ArrayResult implements ResultInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @var array<string, string>
     */
    private array $errors;

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    public function __construct(array $data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function isValid(): bool
    {
        return !$this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
