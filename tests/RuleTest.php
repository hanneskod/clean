<?php

namespace hanneskod\clean;

class RuleTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnNoDefault()
    {
        $this->expectException(Exception::class);
        (new Rule)->validate(null);
    }

    public function testDefaultValue()
    {
        $this->assertSame(
            'foobar',
            (new Rule)->def('foobar')->validate(null)
        );
    }

    public function testSinglePreFilter()
    {
        $this->assertSame(
            'foobar',
            (new Rule)->pre('trim')->validate(' foobar ')
        );
    }

    public function testMultiplePreFilters()
    {
        $this->assertSame(
            'FOOBAR',
            (new Rule)->pre('trim')->pre('strtoupper')->validate(' foobar ')
        );
    }

    public function testMultiplePreFiltersInOneCall()
    {
        $this->assertSame(
            'FOOBAR',
            (new Rule)->pre('trim', 'strtoupper')->validate(' foobar ')
        );
    }

    public function testSingleMatch()
    {
        $this->assertSame(
            '12345',
            (new Rule)->match('ctype_digit')->validate('12345')
        );
    }

    public function testMultipleMatchers()
    {
        $this->assertSame(
            '12345',
            (new Rule)->match('ctype_digit')->match('is_string')->validate('12345')
        );
    }

    public function testMultipleMatchersInOneCall()
    {
        $this->assertSame(
            '12345',
            (new Rule)->match('ctype_digit', 'is_string')->validate('12345')
        );
    }

    public function testExceptionOnMatchFailure()
    {
        $this->expectException(Exception::class);
        (new Rule)->match('ctype_digit')->validate('foobar');
    }

    public function testExceptionOnLateMatchFailure()
    {
        $this->expectException(Exception::class);
        (new Rule)->match('is_string', 'ctype_digit')->validate('foobar');
    }

    public function testSinglePostFilter()
    {
        $this->assertSame(
            '7b',
            (new Rule)->match('ctype_digit')->post('dechex')->validate('123')
        );
    }

    public function testMultiplePostFilters()
    {
        $this->assertSame(
            '7B',
            (new Rule)->match('ctype_digit')->post('dechex')->post('strtoupper')->validate('123')
        );
    }

    public function testMultiplePostFiltersInOneCall()
    {
        $this->assertSame(
            '7B',
            (new Rule)->match('ctype_digit')->post('dechex', 'strtoupper')->validate('123')
        );
    }

    public function testCustomExceptionMessage()
    {
        $this->expectException(Exception::class, 'my-custom-exception-message');
        (new Rule)->msg('my-custom-exception-message')->validate(null);
    }

    public function testCallbackOnException()
    {
        $this->assertSame(
            'foobar',
            (new Rule)->match('ctype_digit')->onException(function () {
                return 'foobar';
            })->validate('foo')
        );
    }
}
