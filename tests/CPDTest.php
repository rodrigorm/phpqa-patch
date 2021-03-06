<?php

namespace Rodrigorm\PHPQAPatch;

use PHPUnit_Framework_TestCase;

class CPDTest extends PHPUnit_Framework_TestCase
{
    public function testPatchCpdIsCalculatedCorrectly()
    {
        $patchcpd = new CPD;

        $this->assertEquals(
            array(
                new \SimpleXMLElement('<duplication lines="1" tokens="10"><file path="/tmp/Example.php" line="11"/><file path="/tmp/Example1.php" line="26"/></duplication>')
            ),
            $patchcpd->execute(
                __DIR__ . '/fixture/pmd-cpd.xml',
                __DIR__ . '/fixture/patch.txt',
                '/tmp/'
            )
        );
    }
}
