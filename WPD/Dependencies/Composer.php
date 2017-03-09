<?php

namespace WPD\Dependencies;

/**
 * This class allows customizations to a composer.json file
 *
 * @todo Add more customizations
 */
class Composer {

	private $requires = [];

	private $requires_dev = [];

	/**
	 * Return the full composer.json structure
	 *
	 * @return array
	 */
	public function get() {
		return [
			'name' => '26b/wordpress-project',
			'require' => $this->requires,
			'require-dev' => $this->requires_dev,
			'authors' => [
				[
					'name' => 'Rui Sardinha',
					'email' => 'mail@ruisardinha.com',
				]
			],
			'extra'=> [
				'installer-paths'=> [
					'wp/wp-content/mu-plugins/{$name}/' => ['type=>wordpress-muplugin'],
					'wp/wp-content/plugins/{$name}/' => ['type=>wordpress-plugin'],
					'wp/wp-content/themes/{$name}/' => ['type=>wordpress-theme'],
				],
			],
			'config'=> [
				'sort-packages'=> true,
			],
		];
	}

	/**
	 * Adds required libraries
	 *
	 * @param array   $requires Required library with version
	 * @param boolean $is_dev   if true it will add to require-dev
	 */
	public function add_require( array $requires, $is_dev = false ) {
		if ( $is_dev === true ) {
			$this->requires_dev = array_merge( $this->requires_dev, $requires );
		} else {
			$this->requires = array_merge( $this->requires, $requires );
		}
	}
}
