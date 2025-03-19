<?php
declare(strict_types=1);
namespace Ng\Ingus\Controller;

trait Check {

	public function check_mixed_letters( $text ): bool {
		$words           = preg_split( '/\s+|,|\.|;/', $text );
		$cyrillicPattern = '/[а-яё]/iu';
		$latinPattern    = '/[a-z]/i';
		$greekPattern    = '/[α-ω]/iu';
		foreach ( $words as $word ) {
			if ( empty( $word ) ) {
				continue;
			}
			if ( preg_match( $cyrillicPattern, $word ) && preg_match( $latinPattern, $word ) ) {
				return true;
			}
			if ( preg_match( $cyrillicPattern, $word ) && preg_match( $greekPattern, $word ) ) {
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
		if ( 0 === $length ) {
			return false;
		}
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
		echo "\n";
		echo 'text: ' . $text . PHP_EOL;
		$result = false;
		foreach ( NGING_REGEX_RULES as $pattern ) {
			$pattern = '/' . $pattern . '/ium';
			if ( 1 === preg_match( $pattern, $text ) ) {
				echo 'pattern: ' . $pattern . ' => ';
				echo ' true' . PHP_EOL;
				$result = true;
			} else {
			}
		}
		echo true === $result ? ' spam ' : ' not spam ';
		echo PHP_EOL;
		return $result;
	}
	public function normalize( $string ) {
		$string = mb_strtolower( $string );
		$string = preg_replace( '/[^a-zа-яё0-9\+]/u', ' ', $string );
		$string = str_replace( array( "\r", "\n" ), ' ', $string );
		$string = preg_replace( '/\s+/u', ' ', $string );
		return $string;
	}
}
