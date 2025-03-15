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
	);
	use Tests;

	function __construct( $update ) {
		$this->update = $update;
	}

	public function is_spam( $update ) {
		foreach ( $this->test_methods as $method ) {
			if ( $this->$method( $update->getMessage()->getText() ) ) {
				return true;
			}
		}
		return false;
	}
}
