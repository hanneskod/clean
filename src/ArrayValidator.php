<?php

namespace hanneskod\clean;

/**
 * Validate arrays of input data
 */
class ArrayValidator extends Validator
{
    /**
     * @var Validator[] Map of field names to validators
     */
    private $validators = [];

    /**
     * @var boolean Flag if unknown array items should be ignored when validating
     */
    private $ignoreUnknown = false;

    /**
     * Register validators
     *
     * @param Validator[] $validators Map of field names to validators
     */
    public function __construct(array $validators = [])
    {
        foreach ($validators as $name => $validator) {
            $this->addValidator($name, $validator);
        }
    }

    /**
     * Add a validator
     *
     * @param  string    $name Name of field to validate
     * @param  Validator $validator The validator
     * @return self Instance for chaining
     */
    public function addValidator($name, Validator $validator)
    {
        $this->validators[$name] = $validator;
        return $this;
    }

    /**
     * Set flag if unknown items should be ignored when validating
     *
     * @param  boolean $ignoreUnknown
     * @return self Instance for chaining
     */
    public function ignoreUnknown($ignoreUnknown = true)
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
                $exception->pushValidatorName($name);
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
