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
					'WORDPRESS_DB_PASSWORD' => '{db_password}',
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
	}
}
