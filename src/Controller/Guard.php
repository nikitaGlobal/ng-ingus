<?php

declare(strict_types=1);

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
	private $check_methods = array();
	use Check;

	function __construct( $update ) {
		$this->check_methods = array_filter(
			get_class_methods( 'Ng\Ingus\Controller\Check' ),
			function ( $method ) {
				return 0 === strpos( $method, 'check_' );
			}
		);
		$this->update        = $update;
	}

	public function is_spam( $update ) {
		$string = $update->getMessage()->getText();
		// if has image or video, get caption.
		if ( $update->getMessage()->getPhoto() || $update->getMessage()->getVideo() ) {
			$string = $update->getMessage()->getCaption();
		}
		if ( ! is_string( $string ) ) {
			return false;
		}
		if ( 0 === strlen( trim( $string ) ) ) {
			return false;
		}
		foreach ( $this->check_methods as $method ) {
			if ( $this->$method( $this->normalize( $string ) ) ) {
				return true;
			}
		}
		return false;
	}
}
