<?php

namespace hanneskod\clean;

class ArrayValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnException()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(null)->willThrow(new Exception);

        $this->expectException(Exception::class);
        (new ArrayValidator([$validator->reveal()]))->validate([]);
    }

    public function testExceptionOnNonArray()
    {
        $this->expectException(Exception::class);
        (new ArrayValidator)->validate('not-an-array');
    }

    public function testExceptionOnUnknownItem()
    {
        $this->expectException(Exception::class);
        (new ArrayValidator)->validate(['unknown-key' => '']);
    }

    public function testIgnoreUnknownItem()
    {
        $this->assertEquals(
            [],
            (new ArrayValidator)->ignoreUnknown()->validate(['unknown-key' => ''])
        );
    }

    public function testValidate()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate('foo')->willReturn('bar');
        $this->assertSame(
            ['key' => 'bar'],
            (new ArrayValidator(['key' => $validator->reveal()]))->validate(['key' => 'foo'])
        );
    }

    public function testIsCallable()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate('foo')->willReturn('bar');
        $this->assertSame(
            ['key' => 'bar'],
            (new ArrayValidator(['key' => $validator->reveal()]))(['key' => 'foo'])
        );
    }

    public function testCustomExceptionCallback()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate('foo')->willThrow(new Exception);

        $validator = new ArrayValidator(['foo' => $validator->reveal()]);

        $exceptions = [];
        $validator->onException(function (\Exception $exception) use (&$exceptions) {
            $exceptions[] = $exception;
        });

        $validator->validate(['foo' => 'foo', 'bar' => 'bar']);

        $this->assertCount(
            2,
            $exceptions,
            'There should be 2 exceptions, one from the validator and one from bar not being definied'
        );
    }

    public function testCustomExceptionCallbackOnNonCleanException()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate('foo')->willThrow(new \Exception);

        $validator = new ArrayValidator(['foo' => $validator->reveal()]);

        $called = false;
        $validator->onException(function (\Exception $exception) use (&$called) {
            $called = true;
        });

        $validator->validate(['foo' => 'foo']);

        $this->assertTrue(
            $called,
            'The exception should be caught even though it is a base exception'
        );
    }
}
