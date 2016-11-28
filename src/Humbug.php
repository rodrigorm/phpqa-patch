<?php

namespace Rodrigorm\PHPQAPatch;

use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

class Humbug
{
    public function execute($json, $patch, $prefix)
    {
        $result = array();

        $humbug   = json_decode(file_get_contents(realpath($json)), true);
        $parser   = new DiffParser;
        $patch    = $parser->parse(file_get_contents($patch));
        $changes  = array();

        foreach ($patch as $diff) {
            $file           = substr($diff->getTo(), 2);
            $changes[$file] = array();

            foreach ($diff->getChunks() as $chunk) {
                $lineNr = $chunk->getEnd();

                foreach ($chunk->getLines() as $line) {
                    if ($line->getType() == Line::ADDED) {
                        $changes[$file][] = $lineNr;
                    }

                    if ($line->getType() != Line::REMOVED) {
                        $lineNr++;
                    }
                }
            }
        }

        $result = array(
            'summary' => $humbug['summary']
        );

        $types = array('uncovered', 'escaped', 'errored', 'timeouts', 'killed');

        foreach ($types as $type) {
            $result[$type] = array();
            foreach ($humbug[$type] as $violation) {
                $path = str_replace($prefix, '', $violation['file']);

                $line = (int)$violation['line'];
                if (isset($changes[$path]) && in_array($line, $changes[$path])) {
                    $result[$type][] = $violation;
                }
            }
        }

        $result['summary']['notests'] = count($result['uncovered']);
        $result['summary']['escapes'] = count($result['escaped']);
        $result['summary']['errors'] = count($result['errored']);
        $result['summary']['timeouts'] = count($result['timeouts']);
        $result['summary']['kills'] = count($result['killed']);
        $result['summary']['total'] =
            $result['summary']['notests'] +
            $result['summary']['escapes'] +
            $result['summary']['errors'] +
            $result['summary']['timeouts'] +
            $result['summary']['kills'];

        return $result;
    }
}
