<?php

declare(strict_types = 1);

namespace hanneskod\clean;

final class Rule implements ValidatorInterface
{
    private ?string $default;
    private ?string $errorMsg;

    /**
     * @var array<callable> Pre match filters
     */
    private array $pre;

    /**
     * @var array<callable> Post match filters
     */
    private array $post;

    /**
     * @var array<callable>
     */
    private array $matchers;

    /**
     * @param array<callable> $pre
     * @param array<callable> $matchers
     * @param array<callable> $post
     */
    public function __construct(
        array $pre = [],
        array $matchers = [],
        array $post = [],
        ?string $default = null,
        ?string $errorMsg = null
    ) {
        $this->pre = $pre;
        $this->post = $post;
        $this->matchers = $matchers;
        $this->default = $default;
        $this->errorMsg = $errorMsg;
    }

    /**
     * Create a new Rule with one or more pre match filters
     *
     * A filter should take a raw value and return the filtered value.
     */
    public function pre(callable ...$pre): Rule
    {
        return new static([...$this->pre, ...$pre], $this->matchers, $this->post, $this->default, $this->errorMsg);
    }

    /**
     * Create a new Rule with one or more matchers
     *
     * A matcher should take a raw value and return true if value is a match
     * and false if it is not.
     */
    public function match(callable ...$match): Rule
    {
        return new static($this->pre, [...$this->matchers, ...$match], $this->post, $this->default, $this->errorMsg);
    }

    /**
     * Create a new Rule with one or more post match filters
     *
     * A filter should take a raw value and return the filtered value.
     */
    public function post(callable ...$post): Rule
    {
        return new static($this->pre, $this->matchers, [...$this->post, ...$post], $this->default, $this->errorMsg);
    }

    /**
     * Create a new Rule with default value
     */
    public function def(string $default): Rule
    {
        return new static($this->pre, $this->matchers, $this->post, $default, $this->errorMsg);
    }

    /**
     * Create a new Rule with exception message
     *
     * %s is replaced with parent exception message
     */
    public function msg(string $errorMsg): Rule
    {
        return new static($this->pre, $this->matchers, $this->post, $this->default, $errorMsg);
    }

    public function applyTo($data): ResultInterface
    {
        try {
            if (is_null($data)) {
                if (!isset($this->default)) {
                    throw new Exception('value missing');
                }

                $data = $this->default;
            }

            foreach ($this->pre as $filter) {
                $data = $filter($data);
            }

            foreach ($this->matchers as $matcherId => $matcher) {
                if (!$matcher($data)) {
                    throw new Exception("matcher #$matcherId failed");
                }
            }

            foreach ($this->post as $filter) {
                $data = $filter($data);
            }

            return new Valid($data);
        } catch (\Throwable $e) {
            if (isset($this->errorMsg)) {
                $e = new Exception(sprintf($this->errorMsg, $e->getMessage()), 0, $e);
            }

            return new Invalid($e);
        }
    }

    public function validate($data)
    {
        return $this->applyTo($data)->getValidData();
    }
}
