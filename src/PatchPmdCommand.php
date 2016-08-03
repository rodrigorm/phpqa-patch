<?php

namespace Rodrigorm\PhpqaPatch;

use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PatchPmdCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('patch-pmd')
             ->addArgument(
                 'xml',
                 InputArgument::REQUIRED,
                 'PHPMD XML Report'
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
        $patchpmd = new PatchPmd;
        $violations = $patchpmd->execute(
            $input->getArgument('xml'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        $output->writeln(sprintf('%d violations found:', count($violations)));
        $output->writeln('');

        foreach ($violations as $violation) {
            foreach ($violation['lines'] as $line) {
                $output->writeln(
                    sprintf(
                        '%s:%s     %s',
                        $violation['file'],
                        $line,
                        $violation['message']
                    )
                );
            }
        }

        if (count($violations)) {
            return 1;
        }
    }
}
