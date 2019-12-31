<?php

namespace hanneskod\clean;

class AbstractValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testExtend()
    {
        $validator = new class extends AbstractValidator {
            protected function create(): ValidatorInterface
            {
                return (new Rule)->match('ctype_digit');
            }
        };

        $this->assertSame(
            '123',
            $validator->validate('123')
        );

        $this->assertFalse(
            $validator->applyTo('abc')->isValid()
        );
    }
}
