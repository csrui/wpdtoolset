<?php

namespace WPD\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use WPD\Generators\Docker;

class GeneratorCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('app:generator')
			->setDescription('Generate Docker Configuration.')
			->addOption(
				'config-only',
				'co',
				InputOption::VALUE_NONE,
				'If you only want the configuration files. Does not run `composer install` or `docker-composer`.'
			)
			->addOption(
				'with-wp',
				null,
				InputOption::VALUE_NONE,
				'Include if you want to manage WordPress with composer.'
			);
			// ->setHelp('This command interfaces with wp-cli')
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//TODO This is supported in PHP 7 only, I'm getting warnings
		$root_dir = dirname( __FILE__, 4 );

		// Ask for a DB Password.
		$output->writeln("<comment>Define a DB password:</comment> (Leave empty to generate one automaticaly)");
		$db_password = readline();

		if ( empty ( $db_password ) ) {
			$db_password = uniqid();
			$output->writeln("<info>Using generated password `{$db_password}` for database<info>");
		} else {
			$output->writeln("<info>Using {$db_password} as the database password</info>");
		}

		// Build configuration.
		$output->writeln( '<info>===========================</info>' );
		$output->writeln( '<info> Building configuration... </info>' );
		$output->writeln( '<info>===========================</info>' );

		$output->writeln( 'Writing docker file' );
		$docker_file = new Docker();
		$docker_file->generate();
		$docker_file->save( 'docker-compose.yml', [
			'db_password' => $db_password,
		] );

		// Copy composer.json from base.
		$output->writeln( 'Writting composer.json' );
		if ( $input->getOption('with-wp') ) {
			copy( $root_dir . '/base/composer-wordpress.json', './composer.json' );
		} else {
			copy( $root_dir . '/base/composer-default.json', './composer.json' );
		}

		// Copy .gitignore from base.
		$output->writeln( 'Writting .gitignore' );
		copy( $root_dir . '/base/.gitignore', './.gitignore' );

		$output->writeln( '<info>Configuration Done!</info>' );

		// Leave if the user only wants the config files.
		if ( $input->getOption('config-only') ) {
			return;
		}

		// Setup environment.
		$output->writeln( '<info>=====================</info>' );
		$output->writeln( '<info> Starting engines... </info>' );
		$output->writeln( '<info>=====================</info>' );

		// To load wordpress as dependency.
		copy( $root_dir . '/base/index.php', './index.php' );
		$res = shell_exec( 'RET=`composer install`;echo $RET' );
		$output->writeln( $res );

		// TODO: WP image is generating wordpress files on the root. Must be stopped!
		$res = shell_exec( 'RET=`docker-compose up -d`;echo $RET' );
		$output->writeln( $res );
	}
}
