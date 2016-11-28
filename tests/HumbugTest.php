<?php

namespace Rodrigorm\PHPQAPatch;

use PHPUnit_Framework_TestCase;

class HumbugTest extends PHPUnit_Framework_TestCase
{
    public function testPatchHumbugIsCalculatedCorrectly()
    {
        $patchhumbug = new Humbug;

        $this->assertEquals(
            array(
                'summary' => array(
                    'total' => 5,
                    'kills' => 1,
                    'escapes' => 1,
                    'errors' => 1,
                    'timeouts' => 1,
                    'notests' => 1,
                    'covered_score' => 79,
                    'combined_score' => 39,
                    'mutation_coverage' => 49,
                ),
                'uncovered' => array(
                    array(
                        'file' => 'tmp/Example.php',
                        'mutator' => '\\Humbug\\Mutator\\Boolean\\LogicalNot',
                        'class' => 'Example',
                        'method' => 'foo',
                        'line' => 6
                    ),
                ),
                'escaped' => array(
                    array(
                        'file' => 'tmp/Example.php',
                        'mutator' => '\\Humbug\\Mutator\\Arithmetic\\Subtraction',
                        'class' => 'Example',
                        'method' => 'bar',
                        'line' => 6,
                        'diff' => '--diff',
                        'tests' => [
                            'ExampleTest::testBar'
                        ],
                        'stderr' => '',
                        'stdout' => 'TAP version 13'
                    )
                ),
                'errored' => array(
                    array(
                        'file' => 'tmp/Example.php',
                        'mutator' => '\\Humbug\\Mutator\\ConditionalNegation\\Equal',
                        'class' => 'Example',
                        'method' => 'bar',
                        'line' => 11,
                        'diff' => '--diff',
                        'tests' => array(
                            'ExampleTest::testBar'
                        ),
                        'stderr' => 'PHP Warning',
                        'stdout' => 'TAP version 13'
                    )
                ),
                'timeouts' => array(
                    array(
                        'file' => 'tmp/Example.php',
                        'mutator' => '\\Humbug\\Mutator\\Increment\\Increment',
                        'class' => 'Example',
                        'method' => 'bar',
                        'line' => 11,
                        'diff' => '--diff',
                        'tests' => [
                            'ExampleTest::testBar'
                        ],
                        'stderr' => '',
                        'stdout' => 'TAP version 13'
                    )
                ),
                'killed' => array(
                    array(
                        'file' => 'tmp/Example.php',
                        'mutator' => '\\Humbug\\Mutator\\Number\\IntegerValue',
                        'class' => 'Example',
                        'method' => 'bar',
                        'line' => 11,
                        'diff' => '--diff',
                        'tests' => [
                            'ExampleTest::testBar'
                        ],
                        'stderr' => '',
                        'stdout' => 'TAP version 13'
                    )
                ),
            ),
            $patchhumbug->execute(
                __DIR__ . '/fixture/humbug.json',
                __DIR__ . '/fixture/patch.txt',
                'tmp/'
            )
        );
    }
}
