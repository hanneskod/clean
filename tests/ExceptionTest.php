<?php

namespace hanneskod\clean;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceRuleName()
    {
        $exception = new Exception;

        $this->assertSame(
            '',
            $exception->getSourceRuleName()
        );

        $exception->pushRuleName('foo');

        $this->assertSame(
            'foo',
            $exception->getSourceRuleName()
        );

        $exception->pushRuleName('bar');

        $this->assertSame(
            'bar::foo',
            $exception->getSourceRuleName()
        );
    }
}
