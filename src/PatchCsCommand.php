<?php

namespace Rodrigorm\PhpqaPatch;

use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PatchCsCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('patch-cs')
             ->addArgument(
                 'xml',
                 InputArgument::REQUIRED,
                 'PHPCS XML Report'
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
        $patchcs = new PatchCs;
        $errors = $patchcs->execute(
            $input->getArgument('xml'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        $output->writeln(sprintf('%d errors found:', count($errors)));
        $output->writeln('');

        foreach ($errors as $error) {
            $output->writeln(
                sprintf(
                    '%s:%s - %s: %s',
                    $error['file'],
                    $error['line'],
                    strtoupper($error['severity']),
                    $error['message']
                )
            );
        }
    }
}
