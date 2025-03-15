<?php
namespace Ng\Ingus\Controller;

use Phpfastcache\Helper\Psr16Adapter as CacheAdapter;

class Cache {

	/**
	 * Cache adapter.
	 *
	 * @var
	 */
	private $cache;

	function __construct() {
		$this->cache = new CacheAdapter( 'Files' );
	}
	public function set( $key, $value, $ttl ) {
		$this->cache->set( $key, $value, $ttl );

		return $value;
	}
	public function get( $key ) {
		return $this->cache->get( $key );
	}
}
