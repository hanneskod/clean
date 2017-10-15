<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Validate arrays of input data
 */
class ArrayValidator extends AbstractValidator
{
    /**
     * @var ValidatorInterface[] Map of field names to validators
     */
    private $validators = [];

    /**
     * @var boolean Flag if unknown array items should be ignored when validating
     */
    private $ignoreUnknown = false;

    /**
     * Register validators
     *
     * @param ValidatorInterface[] $validators Map of field names to validators
     */
    public function __construct(array $validators = [])
    {
        foreach ($validators as $name => $validator) {
            $this->addValidator((string)$name, $validator);
        }
    }

    /**
     * Add a validator
     */
    public function addValidator(string $name, ValidatorInterface $validator): self
    {
        $this->validators[$name] = $validator;
        return $this;
    }

    /**
     * Set flag if unknown items should be ignored when validating
     */
    public function ignoreUnknown(bool $ignoreUnknown = true): self
    {
        $this->ignoreUnknown = $ignoreUnknown;
        return $this;
    }

    /**
     * Validate tainted data
     *
     * {@inheritdoc}
     */
    public function validate($tainted)
    {
        if (!is_array($tainted)) {
            throw new Exception("expecting array input");
        }

        $clean = [];

        foreach ($this->validators as $name => $validator) {
            try {
                $clean[$name] = $validator->validate(
                    isset($tainted[$name]) ? $tainted[$name] : null
                );
            } catch (Exception $exception) {
                $exception->pushValidatorName((string)$name);
                $this->fireException($exception);
            } catch (\Exception $exception) {
                $this->fireException($exception);
            }
        }

        if (!$this->ignoreUnknown && $diff = array_diff_key($tainted, $this->validators)) {
            $this->fireException(
                new Exception('Unknown input item(s): ' . implode(array_keys($diff), ', '))
            );
        }

        return $clean;
    }
}
