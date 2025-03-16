<?php
declare( strict_types = 1 );
namespace Ng\Ingus\Controller;

class Cache {

	/**
	 * Cache adapter.
	 *
	 * @var array
	 */
	private array $cache;
	/**
	 * Cache file.
	 *
	 * @var string
	 */
	private string $cache_file;

	function __construct( string $file ) {
		$this->cache_file = $file;
		if ( ! file_exists( $this->cache_file ) ) {
			file_put_contents( $this->cache_file, json_encode( array() ) );
		}
		$this->delete_expired_and_load_cache();
	}
	public function set( string $key, $value, int $ttl ) {
		$this->cache[ $key ] = array(
			'value' => $value,
			'ttl'   => time() + $ttl,
		);
		file_put_contents( $this->cache_file, json_encode( $this->cache ) );
		return $value;
	}
	public function get( string $key ) {
		if ( ! isset( $this->cache[ $key ] ) ) {
			return false;
		}
		if ( $this->cache[ $key ]['ttl'] < time() ) {
			unset( $this->cache[ $key ] );
			file_put_contents( $this->cache_file, json_encode( $this->cache ) );
			return false;
		}
		return $this->cache[ $key ]['value'];
	}

	private function delete_expired_and_load_cache() {
		$this->cache = json_decode( file_get_contents( $this->cache_file ), true );
		foreach ( $this->cache as $key => $value ) {
			if ( $value['ttl'] < time() ) {
				unset( $this->cache[ $key ] );
			}
		}
		file_put_contents( $this->cache_file, json_encode( $this->cache ) );
	}
}
