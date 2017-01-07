<?php

namespace hanneskod\clean;

/**
 * Test code examples in README.md
 *
 * @coversNothing
 */
class ReadmeIntegration extends \PHPUnit_Framework_TestCase
{
    public function testReadmeIntegrationTests()
    {
        if (!class_exists('hanneskod\readmetester\PHPUnit\AssertReadme')) {
            $this->markTestSkipped('Readme-tester is not available.');
        }

        (new \hanneskod\readmetester\PHPUnit\AssertReadme($this))->assertReadme('README.md');
    }
}
