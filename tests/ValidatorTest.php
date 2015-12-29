<?php

namespace hanneskod\clean;

/**
 * @covers hanneskod\clean\Validator
 */
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
}
