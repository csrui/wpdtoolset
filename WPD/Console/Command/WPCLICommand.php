<?php

namespace WPD\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WPCLICommand extends Command
{
    protected function configure()
    {
        $this
        	->setName('app:wpcli')
			->setDescription('Install WordPress.')
        	->setHelp('This command interfaces with wp-cli')
        	->addArgument('run', InputArgument::REQUIRED, 'WP-CLI command to run?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    	$base = 'RET=`docker-compose run wpcli %s`;echo $RET';

    	$args = $input->getArgument('run');
    	
    	$output->writeln('Running WP-CLI:');

    	$output->writeln( $args );
        $res = shell_exec(sprintf($base, $args));
        $output->writeln($res);
    }
}