<?php
declare( strict_types = 1 );
namespace Ng\Ingus\Controller;

use Ng\Ingus\Controller\Tests;

class Guard {
	/**
	 * Update object.
	 *
	 * @var object
	 */
	private object $update;

	/**
	 * Test methods.
	 *
	 * @var array
	 */
	private $test_methods = array(
		'test_regex',
		'test_emoji',
	);
	use Tests;

	function __construct( $update ) {
		$this->update = $update;
	}

	public function is_spam( $update ) {
		$string = $update->getMessage()->getText();
		if ( ! is_string( $string ) ) {
			return true;
		}
		if ( 0 === strlen( trim( $string ) ) ) {
			return true;
		}
		foreach ( $this->test_methods as $method ) {
			if ( $this->$method( $string ) ) {
				return true;
			}
		}
		return false;
	}
}
