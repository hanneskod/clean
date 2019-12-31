<?php

namespace hanneskod\clean;

class ArrayResultTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        $this->assertTrue(
            (new ArrayResult([], []))->isValid()
        );
    }

    public function testIsNotValid()
    {
        $this->assertFalse(
            (new ArrayResult([], ['an error']))->isValid()
        );
    }

    public function testGetValidData()
    {
        $this->assertSame(
            ['foobar'],
            (new ArrayResult(['foobar'], []))->getValidData()
        );
    }

    public function testGetErrors()
    {
        $this->assertSame(
            ['foobar'],
            (new ArrayResult([], ['foobar']))->getErrors()
        );
    }
}
