<?php

namespace hanneskod\clean;

class ArrayValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionOnException()
    {
        $validator = $this->prophesize('hanneskod\clean\Validator');
        $validator->validate(null)->willThrow(new Exception);

        $this->setExpectedException('hanneskod\clean\Exception');
        (new ArrayValidator([$validator->reveal()]))->validate([]);
    }

    public function testExceptionOnNonArray()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
        (new ArrayValidator)->validate('not-an-array');
    }

    public function testExceptionOnUnknownItem()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
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
        $validator = $this->prophesize('hanneskod\clean\Validator');
        $validator->validate('foo')->willReturn('bar');
        $this->assertSame(
            ['key' => 'bar'],
            (new ArrayValidator(['key' => $validator->reveal()]))->validate(['key' => 'foo'])
        );
    }

    public function testCustomExceptionCallback()
    {
        $validator = $this->prophesize('hanneskod\clean\Validator');
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
}
