<?php

namespace hanneskod\clean;

class InvalidTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        $this->assertFalse(
            (new Invalid(new Exception))->isValid()
        );
    }

    public function testGetValidData()
    {
        $this->expectException(Exception::class);
        (new Invalid(new Exception))->getValidData();
    }

    public function testGetErrors()
    {
        $this->assertSame(
            ['foobar'],
            (new Invalid(new Exception('foobar')))->getErrors()
        );
    }
}
