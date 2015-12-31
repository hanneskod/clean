<?php

namespace hanneskod\clean;

/**
 * Base validator including exception handling using a callback
 */
abstract class Validator
{
    /**
     * @var callable Callback on exception
     */
    private $onExceptionCallback = [__CLASS__, 'defaultOnExceptionCallback'];

    /**
     * Validate tainted data
     *
     * @param  mixed $tainted
     * @return mixed The cleaned data
     * @throws ValidationException If validation fails
     */
    abstract public function validate($tainted);

    /**
     * Register on-exception callback
     *
     * The callback should take an \Exception object and proccess it as
     * appropriate. This generally means throwing an exception of some kind
     * or returning a replacement value.
     *
     * @param  callable $callback
     * @return self Instance for chaining
     */
    public function onException(callable $callback)
    {
        $this->onExceptionCallback = $callback;
        return $this;
    }

    /**
     * Call the on-exception callback
     *
     * @param  \Exception $exception
     * @return mixed Whatever the callback returns
     */
    protected function fireException(\Exception $exception)
    {
        return call_user_func($this->onExceptionCallback, $exception);
    }

    /**
     * Simple callback that throws exceptions
     *
     * @param  \Exception $exception
     * @return void Never returns
     * @throws \Exception throws supplied exception
     */
    protected static function defaultOnExceptionCallback(\Exception $exception)
    {
        throw $exception;
    }
}
