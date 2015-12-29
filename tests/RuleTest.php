<?php

namespace hanneskod\clean;

/**
 * @covers hanneskod\clean\Rule
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionOnNoDefault()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
        (new Rule)->validate(null);
    }

    public function testDefault()
    {
        $this->assertSame(
            'foobar',
            (new Rule)->def('foobar')->validate(null)
        );
    }

    public function testPreFilter()
    {
        $this->assertSame(
            'foobar',
            (new Rule)->pre('trim')->validate(' foobar ')
        );
    }

    public function testExceptionOnMatchFailure()
    {
        $this->setExpectedException('hanneskod\clean\Exception');
        (new Rule)->match('ctype_digit')->validate('foobar');
    }

    public function testMatch()
    {
        $this->assertSame(
            '12345',
            (new Rule)->match('ctype_digit')->validate('12345')
        );
    }

    public function testPostFilter()
    {
        $this->assertSame(
            '&lt;&gt;',
            (new Rule)->post('htmlentities')->validate('<>')
        );
    }

    public function testCustomExceptionMessage()
    {
        $this->setExpectedException(
            'hanneskod\clean\Exception',
            'my-custom-exception-message'
        );
        (new Rule)->msg('my-custom-exception-message')->validate(null);
    }

    public function testCallbackOnException()
    {
        $this->assertSame(
            'bar',
            (new Rule)->match('ctype_digit')->onException(function () {
                return 'bar';
            })->validate('foo')
        );
    }
}
