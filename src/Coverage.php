<?php

namespace Rodrigorm\PHPQAPatch;

use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

class Coverage
{
    public function execute($xml, $patch, $prefix)
    {
        $result = array();

        $clover   = simplexml_load_file(realpath($xml));
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

        foreach ($clover->project->package as $package) {
            foreach ($package->file as $file) {
                $path = str_replace($prefix, '', $file->attributes()->name);
                $lines = array();

                foreach ($file->line as $line) {
                    if ((int)$line->attributes()->count > 0) {
                        continue;
                    }

                    $line = (int)$line->attributes()->num;

                    if (isset($changes[$path]) && in_array($line, $changes[$path])) {
                        $lines[] = $line;
                    }
                }

                if (empty($lines)) {
                    continue;
                }

                $result[] = [
                    'path' => $path,
                    'lines' => $lines,
                ];
            }
        }

        return $result;
    }
}
