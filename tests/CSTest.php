<?php

namespace Rodrigorm\PHPQAPatch;

use PHPUnit_Framework_TestCase;

class CSTest extends PHPUnit_Framework_TestCase
{
    public function testPatchCsIsCalculatedCorrectly()
    {
        $patchcs = new CS;

        $this->assertEquals(
            array(
                array(
                    'file' => 'Example.php',
                    'line' => 11,
                    'severity' => 'error',
                    'message' => 'Lorem ipsum dolor sit amet.'
                )
            ),
            $patchcs->execute(
                __DIR__ . '/fixture/checkstyle.xml',
                __DIR__ . '/fixture/patch.txt',
                '/tmp/'
            )
        );
    }
}
