<?php

namespace hanneskod\clean;

class ValidTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        $this->assertTrue(
            (new Valid(''))->isValid()
        );
    }

    public function testGetValidData()
    {
        $this->assertSame(
            'foobar',
            (new Valid('foobar'))->getValidData()
        );
    }

    public function testGetErrors()
    {
        $this->assertSame(
            [],
            (new Valid('foobar'))->getErrors()
        );
    }
}
