<?php

namespace Rodrigorm\PhpqaPatch;

use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

class PatchPmd
{
    public function execute($xml, $patch, $prefix)
    {
        $result = array();

        $pmd      = simplexml_load_file(realpath($xml));
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

        foreach ($pmd->file as $file) {
            $path = str_replace($prefix, '', $file->attributes()->name);

            foreach ($file->violation as $violation) {
                $beginline = (int)$violation->attributes()->beginline;
                $endline = (int)$violation->attributes()->endline;
                $lines = array();

                for ($line = $beginline; $line <= $endline; $line++) {
                    if (isset($changes[$path]) && in_array($line, $changes[$path])) {
                        $lines[] = $line;
                    }
                }

                if (empty($lines)) {
                    continue;
                }

                $result[] = array(
                    'file' => $path,
                    'message' => trim((string)$violation),
                    'lines' => $lines
                );
            }
        }

        return $result;
    }
}
