<?php

namespace hanneskod\clean;

/**
 * Test code examples in README.md
 *
 * @coversNothing
 */
class ReadmeTest extends \hanneskod\readmetester\ReadmeTestCase
{
    public function testReadmeExamples()
    {
        $this->assertReadme('README.md');
    }
}
