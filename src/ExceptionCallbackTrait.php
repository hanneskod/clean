<?php

namespace hanneskod\clean;

/**
 * Exception handling using a callback
 */
trait ExceptionCallbackTrait
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
     * Simple callback that throws exceptions
     *
     * @param  \Exception $exception
     * @return void Never returns
     * @throws \Exception throws supplied exception
     */
    protected function defaultOnExceptionCallback(\Exception $exception)
    {
        throw $exception;
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
}
