<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Defines a validation rule
 */
class Rule extends Validator
{
    /**
     * @var string Default value
     */
    private $default;

    /**
     * @var callable[] Registered pre match filters
     */
    private $preFilters = [];

    /**
     * @var callable[] Registered post match filters
     */
    private $postFilters = [];

    /**
     * @var callable[] Registered matchers
     */
    private $matchers = [];

    /**
     * @var string Exception message on validation failure
     */
    private $exceptionMessage = 'Validation failed: %s';

    /**
     * Setup on-exception callback
     */
    public function __construct()
    {
        $this->onException(function (\Exception $exception) {
            throw new Exception(sprintf($this->exceptionMessage, $exception->getMessage()), 0, $exception);
        });
    }

    /**
     * Set default value
     */
    public function def($default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Register one or more pre match filters
     *
     * A filter should take a string value and return the filtered value.
     */
    public function pre(callable... $filters): self
    {
        foreach ($filters as $filter) {
            $this->preFilters[] = $filter;
        }
        return $this;
    }

    /**
     * Register one or more post match filters
     *
     * A filter should take a string value and return the filtered value.
     */
    public function post(callable... $filters): self
    {
        foreach ($filters as $filter) {
            $this->postFilters[] = $filter;
        }
        return $this;
    }

    /**
     * Register one or more matchers
     *
     * A matcher should take a string value and return true if value is a match
     * and false if it is not.
     *
     * @param  callable ...$matcher Any number of matchers
     * @return self Instance for chaining
     */
    public function match(callable... $matchers): self
    {
        foreach ($matchers as $matcher) {
            $this->matchers[] = $matcher;
        }
        return $this;
    }

    /**
     * Set exception message
     *
     * Note that if a custom exception callback is registered using onException
     * setting this exception message will have no effect.
     *
     * @param string $exceptionMessage Custom exception message, %s is replaced with
     *     parent exception message
     */
    public function msg(string $exceptionMessage): self
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /**
     * Validate value
     *
     * {@inheritdoc}
     */
    public function validate($value)
    {
        try {
            if (is_null($value)) {
                if (!isset($this->default)) {
                    throw new Exception('value missing');
                }
                $value = $this->default;
            }

            foreach ($this->preFilters as $filter) {
                $value = $filter($value);
            }

            foreach ($this->matchers as $matcherId => $matcher) {
                if (!$matcher($value)) {
                    throw new Exception("matcher #$matcherId failed");
                }
            }

            foreach ($this->postFilters as $filter) {
                $value = $filter($value);
            }

            return $value;
        } catch (\Exception $exception) {
            return $this->fireException($exception);
        }
    }
}
