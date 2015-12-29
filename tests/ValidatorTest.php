<?php

namespace hanneskod\clean;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionOnException()
    {
        $rule = $this->prophesize('hanneskod\clean\RuleInterface');
        $rule->validate(null)->willThrow(new Exception);

        $this->setExpectedException('hanneskod\clean\Exception');
        (new Validator([$rule->reveal()]))->validate([]);
    }

    public function testExceptionOnNonArray()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
        (new Validator)->validate('not-an-array');
    }

    public function testExceptionOnUnknownItem()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
        (new Validator)->validate(['unknown-key' => '']);
    }

    public function testIgnoreUnknownItem()
    {
        $this->assertEquals(
            [],
            (new Validator)->ignoreUnknown()->validate(['unknown-key' => ''])
        );
    }

    public function testValidate()
    {
        $rule = $this->prophesize('hanneskod\clean\RuleInterface');
        $rule->validate('foo')->willReturn('bar');
        $this->assertSame(
            ['key' => 'bar'],
            (new Validator(['key' => $rule->reveal()]))->validate(['key' => 'foo'])
        );
    }

    public function testCustomExceptionCallback()
    {
        $rule = $this->prophesize('hanneskod\clean\RuleInterface');
        $rule->validate('foo')->willThrow(new Exception);

        $validator = new Validator(['foo' => $rule->reveal()]);

        $exceptions = [];
        $validator->onException(function (\Exception $exception) use (&$exceptions) {
            $exceptions[] = $exception;
        });

        $validator->validate(['foo' => 'foo', 'bar' => 'bar']);

        $this->assertCount(
            2,
            $exceptions,
            'There should be 2 exceptions, one from the rule and one from bar not being definied'
        );
    }
}
