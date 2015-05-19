<?php

namespace Rodrigorm\PhpqaPatch;

use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

class PatchCs
{
    public function execute($xml, $patch, $prefix)
    {
        $result = array();

        $cs       = simplexml_load_file(realpath($xml));
        $parser   = new DiffParser;
        $patch    = $parser->parse(file_get_contents($patch));
        $changes  = array();

        foreach ($patch as $diff) {
            $file           = substr($diff->getFrom(), 2);
            $changes[$file] = array();

            foreach ($diff->getChunks() as $chunk) {
                $lineNr = $chunk->getStart();

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

        foreach ($cs ->file as $file) {
            $path = str_replace($prefix, '', $file->attributes()->name);

            foreach ($file->error as $error) {
                $line = (int)$error->attributes()->line;
                if (isset($changes[$path]) && in_array($line, $changes[$path])) {
                    $result[] = array(
                        'file' => $path,
                        'line' => $line,
                        'severity' => (string)$error->attributes()->severity,
                        'message' => (string)$error->attributes()->message
                    );
                }
            }
        }

        return $result;
    }
}
