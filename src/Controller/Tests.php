<?php
declare(strict_types=1);
namespace Ng\Ingus\Controller;

trait Tests {

	/**
	 *  if string contains 3 or more emoji return false
	 *
	 * @param string $text
	 *
	 * @return bool
	 */
	public function test_emoji( $text ) {
		$pattern = '/[\x{1F300}-\x{1F5FF}\x{1F900}-\x{1F9FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E0}-\x{1F1FF}]/um';
		preg_match_all( $pattern, $text, $matches );
		if ( 3 <= count( $matches[0] ) ) {
			return true;
		}
		return false;
	}
	public function test_regex( $text ) {
		if ( ! is_string( $text ) ) {
			return false;
		}
		foreach ( NGING_REGEX_RULES as $pattern ) {
			if ( 1 === preg_match( $pattern, $text ) ) {
				return true;
			}
			$string = mb_strtolower( $text );
			if ( 1 === preg_match( $pattern, $string ) ) {
				return true;
			}
		}
	}
}
