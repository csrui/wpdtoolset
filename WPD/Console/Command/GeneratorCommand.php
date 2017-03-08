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

		//TODO Get password from $input
		$db_password = 'bananarama';

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

		$yaml = Yaml::dump($docker_file);
		$output->writeln('Writing docker file');
		file_put_contents('docker-compose.yml', $yaml);

		$gitignore = basename(__DIR__) . PHP_EOL;
		$output->writeln('Writting .gitignore');
		file_put_contents('.gitignore', $gitignore);

		$output->writeln('Writting composer.json');
		copy( 'toolset/base/composer.json', './composer.json' );

		$output->writeln('Starting engines...');

		// To load wordpress as dependency
		copy( 'toolset/base/index.php', './index.php' );
		$res = shell_exec('RET=`composer update`;echo $RET');
		$output->writeln($res);

		//TODO WP image is generating wordpress files on the root. Must be stopped
		$res = shell_exec('RET=`docker-compose up`;echo $RET');
		$output->writeln($res);
	}
}
