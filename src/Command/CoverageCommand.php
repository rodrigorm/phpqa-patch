<?php
namespace Rodrigorm\PHPQAPatch\Command;

use Rodrigorm\PHPQAPatch\Coverage;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('coverage')
             ->addArgument(
                 'xml',
                 InputArgument::REQUIRED,
                 'Clover generated coverage file in XML format.'
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
        $patchcoverage = new Coverage;
        $result = $patchcoverage->execute(
            $input->getArgument('xml'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        if (empty($result)) {
            $output->writeln('No uncovered files found.');
            return 0;
        }

        $output->writeln(sprintf('%d uncovered files found:', count($result)));

        foreach ($result as $uncovered) {
            $output->writeln($uncovered['path']);
            $output->writeln('   ' . implode(', ', $uncovered['lines']));
        }

        return 1;
    }
}
