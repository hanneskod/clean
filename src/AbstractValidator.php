<?php

declare(strict_types = 1);

namespace hanneskod\clean;

/**
 * Base validator including exception handling using a callback
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var callable Callback on exception
     */
    private $onExceptionCallback = [__CLASS__, 'defaultOnExceptionCallback'];

    /**
     * Register on-exception callback
     *
     * The callback should take an \Exception object and proccess it as
     * appropriate. This generally means throwing an exception of some kind
     * or returning a replacement value.
     */
    public function onException(callable $callback): self
    {
        $this->onExceptionCallback = $callback;
        return $this;
    }

    /**
     * Call the on-exception callback
     *
     * @return mixed Whatever the callback returns
     */
    protected function fireException(\Exception $exception)
    {
        return call_user_func($this->onExceptionCallback, $exception);
    }

    /**
     * Simple callback that throws exceptions
     *
     * @throws \Exception throws supplied exception
     */
    protected static function defaultOnExceptionCallback(\Exception $exception): void
    {
        throw $exception;
    }

    public function __invoke($tainted)
    {
        return $this->validate($tainted);
    }
}
