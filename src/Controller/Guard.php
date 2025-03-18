<?php
declare( strict_types = 1 );
namespace Ng\Ingus\Controller;

use Ng\Ingus\Controller\Check;

class Guard {
	/**
	 * Update object.
	 *
	 * @var object
	 */
	private object $update;

	/**
	 * check methods.
	 *
	 * @var array
	 */
	private $check_methods = array(
		'check_regex',
		'check_emoji',
	);
	use Check;

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
		foreach ( $this->check_methods as $method ) {
			if ( $this->$method( $string ) ) {
				return true;
			}
		}
		return false;
	}
}
