<?php

namespace Rodrigorm\PhpqaPatch;

use PHPUnit_Framework_TestCase;

class PatchPmdTest extends PHPUnit_Framework_TestCase
{
    public function testPatchPmdIsCalculatedCorrectly()
    {
        $patchpmd = new PatchPmd;

        $this->assertEquals(
            array(
                array(
                    'file' => 'Example.php',
                    'message' => 'Lorem ipsum dolor sit amet.',
                    'lines' => array(11)
                )
            ),
            $patchpmd->execute(
                __DIR__ . '/fixture/pmd.xml',
                __DIR__ . '/fixture/patch.txt',
                '/tmp/'
            )
        );
    }
}
