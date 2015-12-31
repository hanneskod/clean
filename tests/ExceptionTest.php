<?php

namespace hanneskod\clean;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceValidatorName()
    {
        $exception = new Exception;

        $this->assertSame(
            '',
            $exception->getSourceValidatorName()
        );

        $exception->pushValidatorName('foo');

        $this->assertSame(
            'foo',
            $exception->getSourceValidatorName()
        );

        $exception->pushValidatorName('bar');

        $this->assertSame(
            'bar::foo',
            $exception->getSourceValidatorName()
        );
    }
}
