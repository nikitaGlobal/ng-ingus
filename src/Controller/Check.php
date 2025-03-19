<?php
declare(strict_types=1);
namespace Ng\Ingus\Controller;

trait Check {

	public function check_mixed_letters( $text ): bool {
		$words           = preg_split( '/\s+|,|\.|;/', $text );
		$cyrillicPattern = '/[а-яё]/iu';
		$latinPattern    = '/[a-z]/i';
		foreach ( $words as $word ) {
			if ( empty( $word ) ) {
				continue;
			}
			if ( preg_match( $cyrillicPattern, $word ) && preg_match( $latinPattern, $word ) ) {
				return true;
			}
		}
		return false;
	}
	/**
	 *  if string contains 3 or more emoji return false
	 *
	 * @param string $text
	 *
	 * @return bool
	 */
	public function check_emoji( $text ) {
		$pattern = '/[\x{1F300}-\x{1F5FF}\x{1F900}-\x{1F9FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E0}-\x{1F1FF}]/um';
		preg_match_all( $pattern, $text, $matches );
		if ( 7 <= count( $matches[0] ) ) {
			return true;
		}
		$length = mb_strlen( $text );
		// if emojis are 15% or more of the text return true.
		if ( 0.15 <= count( $matches[0] ) / $length ) {
			return true;
		}
		return false;
	}
	public function check_regex( $text ) {
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
