<?php
declare(strict_types=1);
namespace Ng\Ingus\Controller;

trait Tests {

	public function test_regex( $text ) {
		if ( ! is_string( $text ) ) {
			return true;
		}
		foreach ( NGING_REGEX_RULES as $pattern ) {
			if ( 1 === preg_match( $pattern, $text ) ) {
				return true;
			}
		}
	}
}
