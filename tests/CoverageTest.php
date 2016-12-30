<?php

namespace Rodrigorm\PHPQAPatch;

use PHPUnit_Framework_TestCase;

class CoverageTest extends PHPUnit_Framework_TestCase
{
    public function testPatchCoverageIsCalculatedCorrectly()
    {
        $patchoverage = new Coverage;

        $this->assertEquals(
            array(
                array(
                    'path' => 'Example.php',
                    'lines' => array(11)
                )
            ),
            $patchoverage->execute(
                __DIR__ . '/fixture/clover.xml',
                __DIR__ . '/fixture/patch.txt',
                '/tmp/'
            )
        );
    }
}
