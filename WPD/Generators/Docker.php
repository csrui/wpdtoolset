<?php

namespace WPD\Generators;

use WPD\Generators\Generator;
use Symfony\Component\Yaml\Yaml;

class Docker extends Generator {

	public function generate( ) {

		// Write docker-compose file.
		// 3 levels of expanded yml syntax
		// 2 spaces for indentation
		$this->set_content( Yaml::dump( $this->get(), 3, 2 ) );
	}

	public function get() {
		return [
			'wpdb' => [
				'image' => 'mariadb',
				'ports' => [
					'8081:3306',
				],
				'environment' => [
					'MYSQL_ROOT_PASSWORD' => '{db_password}',
				],
			],
			'wp' => [
				'image'   => 'wodby/php',
				'volumes' => [
					'./:/var/www/html',
				],
				'environment' => [
					'PHP_SENDMAIL_PATH'  => '/usr/sbin/sendmail -t -i -S mailhog:1025',
					'PHP_XDEBUG_ENABLED' => 0,
					'PHP_HOST_NAME'      => 'localhost:8000',
					'PHP_SITE_NAME'      => 'dev',
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
				'command'    => '--info',
			],
			'composer' => [
				'image' => 'composer/composer',
				'volumes_from' => [
					'wp',
				],
				'working_dir' => '/var/www/html',
				'command'     => '--info',
			],
			'mailhog' => [
				'image' => 'mailhog/mailhog',
				'ports' => [
					'8002:8025',
				]
			]
		];
	}
}
