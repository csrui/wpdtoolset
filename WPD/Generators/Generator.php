<?php

namespace WPD\Generators;

abstract class Generator {

	abstract protected function get();

	protected function set_content( $content ) {
		$this->content = $content;
	}

	public function generate() {
		$this->set_content( $this->get() );
	}

	private function add_configuration( array $variables = [] ) {
		$content = $this->content;

		foreach ( $variables as $var => $value ) {
			$replace = sprintf( '{%s}', $var );
			$content = str_replace( $replace, $value, $content );
		}

		return $content;
	}

	public function save( $filename, array $configuration ) {
		$content = $this->add_configuration( $configuration );
		file_put_contents( $filename, $content );
	}
}
