<?php
namespace Rodrigorm\PHPQAPatch\Command;

use Rodrigorm\PHPQAPatch\Humbug;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HumbugCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('humbug')
             ->addArgument(
                 'json',
                 InputArgument::REQUIRED,
                 'Humbug generated log file in JSON format.'
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
        $patchhumbug = new Humbug;
        $result = $patchhumbug->execute(
            $input->getArgument('json'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        $output->writeln(sprintf('%s mutations were generated:', $result['summary']['total']));
        $output->writeln(sprintf('%\' 8s mutants were killed', $result['summary']['kills']));
        $output->writeln(sprintf('%\' 8s mutants were not covered by tests', $result['summary']['notests']));
        $output->writeln(sprintf('%\' 8s covered mutants were not detected', $result['summary']['escapes']));
        $output->writeln(sprintf('%\' 8s fatal errors were encountered', $result['summary']['errors']));
        $output->writeln(sprintf('%\' 8s time outs were encountered', $result['summary']['timeouts']));
        $output->writeln('');
        $output->writeln('Metrics:');
        $output->writeln(sprintf('    Mutation Score Indicator (MSI): %s%%', $result['summary']['combined_score']));
        $output->writeln(sprintf('    Mutation Code Coverage: %s%%', $result['summary']['mutation_coverage']));
        $output->writeln(sprintf('    Covered Code MSI: %s%%', $result['summary']['covered_score']));
        $output->writeln('');

        $this->printViolations($output, $result['uncovered'], 'Uncovered');
        $this->printViolations($output, $result['escaped'], 'Escapes');
        $this->printViolations($output, $result['timeouts'], 'Timeouts');
        $this->printViolations($output, $result['errored'], 'Errors');

        if ($result['summary']['total']) {
            return 1;
        }
    }

    protected function printViolations($output, $violations, $title)
    {
        $output->writeln('------');
        $output->writeln($title);
        $output->writeln('------');
        $output->writeln('');
        $output->writeln('');

        foreach ($violations as $i => $violation) {
            if (empty($violation['diff'])) {
                $output->writeln(sprintf('%s) Mutator %s on %s::%s() in %s on line %s', $i + 1, $violation['mutator'], $violation['class'], $violation['method'], $violation['file'], $violation['line']));
            } else {
                $output->writeln(sprintf('%s) %s', $i + 1, $violation['mutator']));
                $output->writeln(sprintf('Diff on %s::%s() in %s:', $violation['class'], $violation['method'], $violation['file']));
                $output->writeln($violation['diff']);
                $output->writeln('');
            }

            if (!empty($violation['stderr'])) {
                $output->writeln('The following output was received on stderr:');
                $output->writeln('');
                $output->writeln($violation['stderr']);
            }
        }

        $output->writeln('');
        $output->writeln('');
    }
}
