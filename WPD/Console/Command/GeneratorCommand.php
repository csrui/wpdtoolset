<?php

namespace WPD\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

class GeneratorCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('app:generator')
			->setDescription('Generate Docker Configuration.');
			// ->setHelp('This command interfaces with wp-cli')
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$root_dir = dirname( __FILE__, 4 );

		// Ask for a DB Password.
		$output->writeln("<info>Define a DB password:</info> <comment>(Leave empty to use default `bananarama`)</comment>");
		$db_password = readline();

		if ( empty ( $db_password ) ) {
			$output->writeln("<info>Using default password `bananarama` for database<info>");
			$db_password = 'bananarama';
		} else {
			$output->writeln("<info>Using {$db_password} as the database password</info>");
		}

		$docker_file = [
			'wpdb' => [
				'image' => 'mariadb',
				'ports' => [
					'8081:3306',
				],
				'environment' => [
					'MYSQL_ROOT_PASSWORD' => $db_password,
				],
			],
			'wp' => [
				'image' => 'wordpress',
				'volumes' => [
					'./:/var/www/html',
				],
				'ports' => [
					'8080:80',
				],
				'links' => [
					'wpdb:mysql',
				],
				'environment' => [
					'WORDPRESS_DB_PASSWORD' => $db_password,
				],
			],
			'wpcli' => [
				'image' => 'tatemz/wp-cli',
				'volumes_from' => [
					'wp',
				],
				'links' => [
					'wpdb:mysql',
				],
				'entrypoint' => 'wp',
				'command' => '--info',
			],
			'composer' => [
				'image' => 'composer/composer',
				'volumes_from' => [
					'wp',
				],
				'working_dir' => '/var/www/html',
				'command' => '--info',
			],
		];

		// Write docker-compose file.
		$yaml = Yaml::dump( $docker_file );
		$output->writeln( '<info>Writing docker file</info>' );
		file_put_contents( 'docker-compose.yml', $yaml );

		// Copy composer.json from base.
		$output->writeln('<info>Writting .gitignore</info>');
		copy( $root_dir . '/base/composer.json', './composer.json' );

		// Copy .gitignore from base.
		$output->writeln( '<info>Writting composer.json</info>' );
		copy( $root_dir . '/base/composer.json', './composer.json' );

		// Setup environment.
		$output->writeln( '<info>=====================</info>' );
		$output->writeln( '<info> Starting engines... </info>' );
		$output->writeln( '<info>=====================</info>' );

		// To load wordpress as dependency.
		copy( $root_dir . '/base/index.php', './index.php' );
		$res = shell_exec( 'RET=`composer install`;echo $RET' );
		$output->writeln( $res );

		// TODO: WP image is generating wordpress files on the root. Must be stopped!
		$res = shell_exec( 'RET=`docker-compose up`;echo $RET' );
		$output->writeln( $res );
	}
}
