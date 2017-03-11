<?php

namespace WPD\Generators;

/**
 * This class allows customizations to a composer.json file
 *
 * @todo Add more customizations
 */
class Composer extends Generator {

	private $requires = [];

	private $requires_dev = [];

	/**
	 * Defines the file content
	 *
	 * @return void
	 */
	public function generate() {
		$this->set_content( json_encode( $this->get() ) );
	}

	/**
	 * Return the full composer.json structure
	 *
	 * @return array
	 */
	public function get() {
		return [
			'name'        => '26b/wordpress-project',
			'require'     => $this->requires,
			'require-dev' => $this->requires_dev,
			'authors'     => [
				[
					'name'  => 'Rui Sardinha',
					'email' => 'mail@ruisardinha.com',
				]
			],
			'extra' => [
				'wordpress-install-dir' => 'wp',
				'installer-paths' => [
					'wp/wp-content/mu-plugins/{$name}/' => ['type=>wordpress-muplugin'],
					'wp/wp-content/plugins/{$name}/' => ['type=>wordpress-plugin'],
					'wp/wp-content/themes/{$name}/' => ['type=>wordpress-theme'],
				],
			],
			'config' => [
				'sort-packages' => true,
			],
		];
	}

	/**
	 * Helper methods to add runtime dependencies
	 *
	 * @param array $requires Array with dependencies
	 */
	public function add_require( array $requires ) {
		$this->add_dependency( $requires, false );
	}

	/**
	 * Helper methods to add dev dependencies
	 *
	 * @param array $requires Array with dependencies
	 */
	public function add_require_dev( array $requires_dev ) {
		$this->add_dependency( $requires, true );
	}

	/**
	 * Adds required libraries
	 *
	 * @param array   $requires Required library with version
	 * @param boolean $is_dev   if true it will add to require-dev
	 */
	private function add_dependency( array $requires, $is_dev = false ) {
		if ( $is_dev === true ) {
			$this->requires_dev = array_merge( $this->requires_dev, $requires );
		} else {
			$this->requires = array_merge( $this->requires, $requires );
		}
	}
}
