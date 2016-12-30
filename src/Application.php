<?php

namespace Rodrigorm\PHPQAPatch;

use Symfony\Component\Console\Application as AbstractApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Application extends AbstractApplication
{
    public function __construct()
    {
        parent::__construct('phpqa', 'dev-master');

        $this->add(new Command\PMDCommand);
        $this->add(new Command\CPDCommand);
        $this->add(new Command\CSCommand);
        $this->add(new Command\HumbugCommand);
        $this->add(new Command\CoverageCommand);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (!$input->hasParameterOption('--quiet')) {
            $output->write(
                sprintf(
                    "phpqa %s by Rodrigo Moyle.\n\n",
                    $this->getVersion()
                )
            );
        }

        if ($input->hasParameterOption('--version') ||
            $input->hasParameterOption('-V')) {
            exit;
        }

        return parent::doRun($input, $output);
    }
}
