<?php

namespace Rodrigorm\PHPQAPatch\Command;

use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CPDCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('cpd')
             ->addArgument(
                 'xml',
                 InputArgument::REQUIRED,
                 'PHPCPD XML Report'
             )
             ->addOption(
                 'patch',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Unified diff to be analysed for patch coverage'
             )
             ->addOption(
                 'path-prefix',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Prefix that needs to be stripped from paths in the diff'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $patchcpd = new Patch\CPD;
        $duplications = $patchcpd->execute(
            $input->getArgument('xml'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        $output->writeln(sprintf('%d clones found:', count($duplications)));
        $output->writeln('');

        foreach ($duplications as $duplication) {
            foreach ($duplication->file as $file) {
                $beginline = (int)$file->attributes()->line;
                $endline = $beginline + ((int)$duplication->attributes()->lines - 1);
                $output->writeln(sprintf('  -     %s:%d-%d', $file->attributes()->path, $beginline, $endline));
            }
            $output->writeln('');
        }

        if (count($duplications)) {
            return 1;
        }
    }
}
