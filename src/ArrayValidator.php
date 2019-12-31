<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Validate arrays of input data
 */
final class ArrayValidator implements ValidatorInterface
{
    /**
     * @var array<string, ValidatorInterface>
     */
    private array $validators;
    private bool $ignoreUnknown;

    /**
     * @param array<string, ValidatorInterface> $validators
     */
    public function __construct(array $validators = [], bool $ignoreUnknown = false)
    {
        $this->validators = $validators;
        $this->ignoreUnknown = $ignoreUnknown;
    }

    /**
     * Create a new validator flagged if unknown items should be ignored when validating
     */
    public function ignoreUnknown(bool $ignoreUnknown = true): ArrayValidator
    {
        return new static($this->validators, $ignoreUnknown);
    }

    public function applyTo($data): ResultInterface
    {
        if (!is_array($data)) {
            return new Invalid(new Exception("expecting array input"));
        }

        /** @var array<string, mixed> */
        $clean = [];

        /** @var array<string, string> */
        $errors = [];

        foreach ($this->validators as $name => $validator) {
            $result = $validator->applyTo($data[$name] ?? null);

            if ($result->isValid()) {
                $clean[$name] = $result->getValidData();
                continue;
            }

            $errors[$name] = implode("\n", $result->getErrors());
        }

        if (!$this->ignoreUnknown && $diff = array_diff_key($data, $this->validators)) {
            foreach (array_keys($diff) as $unknown) {
                $errors[(string)$unknown] = "Unknown input item $unknown";
            }
        }

        return new ArrayResult($clean, $errors);
    }

    public function validate($data)
    {
        $result = $this->applyTo($data);

        if ($result->isValid()) {
            return $result->getValidData();
        }

        $msg = '';

        foreach ($result->getErrors() as $name => $error) {
            $msg .= "$name: $error\n";
        }

        throw new Exception(trim($msg));
    }

}
