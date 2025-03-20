<?php
declare(strict_types=1);
namespace Ng\Ingus\Controller;

class Cas_Chat {
	private $api_url = 'https://api.cas.chat/check?user_id=';
	private $cache;

	public function __construct( $cache ) {
		$this->cache = $cache;
	}

	public function check_user( int $user_id ): bool {
		$cache_key = 'cas_chat_' . $user_id;
		if ( $result = $this->cache->get( $cache_key ) ) {
			return $result;
		}
		$response         = file_get_contents( $this->api_url . $user_id );
		$response_decoded = json_decode( $response, true );
		if ( ! empty( $response_decoded ) ) {
			$this->cache->set( $cache_key, $response_decoded['ok'], 600 );
			return $response_decoded['ok'];
		}
		throw new \Exception( 'Error checking user in cas.chat: ' . $this->api_url . $user_id );
	}
}
