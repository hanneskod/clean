<?php

namespace hanneskod\clean;

/**
 * Defines a validation rule
 */
class Rule implements RuleInterface
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
     * @var callable Callback on validation failure
     */
    private $onException;

    /**
     * Setup rule
     */
    public function __construct()
    {
        $this->onException = function (Exception $exception) {
            throw $exception;
        };
    }

    /**
     * Set default value
     *
     * @param  string $default
     * @return Rule instance for chaining
     */
    public function def($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Register one or more pre match filters
     *
     * A filter should take a string value and return the filtered value.
     *
     * @param  callable,... $filter Any number of filters
     * @return Rule instance for chaining
     */
    public function pre(callable $filter)
    {
        foreach (func_get_args() as $filter) {
            $this->preFilters[] = $filter;
        }
        return $this;
    }

    /**
     * Register one or more post match filters
     *
     * A filter should take a string value and return the filtered value.
     *
     * @param  callable,... $filter Any number of filters
     * @return Rule instance for chaining
     */
    public function post(callable $filter)
    {
        foreach (func_get_args() as $filter) {
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
     * @param  callable,... $matcher Any number of matchers
     * @return Rule instance for chaining
     */
    public function match(callable $matcher)
    {
        foreach (func_get_args() as $matcher) {
            $this->matchers[] = $matcher;
        }
        return $this;
    }

    /**
     * Set exception message
     *
     * @param string $exceptionMessage Custom exception message, %s is replaced with
     *     parent exception message
     * @return Rule instance for chaining
     */
    public function msg($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }


    /**
     * Register on exception callback
     *
     * The callback should take an Exception object and proccess it as
     * appropriate. This generally means throwing or re-throwing an exception
     * of some kind.
     *
     * @param  callable $callback
     * @return Rule instance for chaining
     */
    public function onException(callable $callback)
    {
        $this->onException = $callback;
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
            return call_user_func(
                $this->onException,
                new Exception(
                    sprintf($this->exceptionMessage, $exception->getMessage()),
                    0,
                    $exception
                )
            );
        }
    }
}
