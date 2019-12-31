<?php

namespace hanneskod\clean;

class ArrayValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnFailingRule()
    {
        $this->expectException(Exception::class);
        (new ArrayValidator([new Rule]))->validate([]);
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
        $this->assertSame(
            ['foo' => 'bar'],
            (new ArrayValidator(['foo' => new Rule]))->validate(['foo' => 'bar'])
        );
    }

    public function testCollectMultipleErrors()
    {
        $validator = new ArrayValidator(['foo' => (new Rule)->match('is_string')]);

        $result = $validator->applyTo(['foo' => null, 'bar' => 'bar']);

        $this->assertCount(2, $result->getErrors());
    }

    public function testExceptionMessage()
    {
        $validator = new ArrayValidator(['foo' => (new Rule)->match(function () {
            throw new Exception('bar');
        })]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('foo: bar');

        $validator->validate(['foo' => '']);
    }

    public function testNonCleanExceptions()
    {
        $validator = new ArrayValidator(['foo' => (new Rule)->match(function () {
            throw new \Exception();
        })]);

        $this->expectException(Exception::class);

        $validator->validate(['foo' => '']);
    }
}
