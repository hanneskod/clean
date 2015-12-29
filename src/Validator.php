<?php

namespace hanneskod\clean;

/**
 * Validate arrays of input data
 */
class Validator implements RuleInterface
{
    /**
     * @var RuleInterface[] Map of field names to rule objects
     */
    private $rules = [];

    /**
     * @var boolean Flag if unknown array items should be ignored when validating
     */
    private $ignoreUnknown = false;

    /**
     * Register rules
     *
     * @param RuleInterface[] $rules Map of field names to Rule objects
     */
    public function __construct(array $rules = [])
    {
        foreach ($rules as $name => $rule) {
            $this->addRule($name, $rule);
        }
    }

    /**
     * Add a rule
     *
     * @param  string        $name Name of field this rule should match
     * @param  RuleInterface $rule The rule
     * @return Validator instance for chaining
     */
    public function addRule($name, RuleInterface $rule)
    {
        $this->rules[$name] = $rule;
        return $this;
    }

    /**
     * Set flag if unknown items shoudl be ignored when validating
     *
     * @param  boolean $ignoreUnknown
     * @return Validator instance for chaining
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

        foreach ($this->rules as $name => $rule) {
            try {
                $clean[$name] = $rule->validate(
                    isset($tainted[$name]) ? $tainted[$name] : null
                );
            } catch (Exception $exception) {
                $exception->pushRuleName($name);
                throw $exception;
            }
        }

        if (!$this->ignoreUnknown && $diff = array_diff_key($tainted, $clean)) {
            throw new Exception('Unknown input item(s): ' . implode(array_keys($diff), ', '));
        }

        return $clean;
    }
}
