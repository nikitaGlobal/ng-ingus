<?php
/**
 * Main app file.
 */
require_once 'vendor/autoload.php';
date_default_timezone_set( 'Europe/Moscow' );
define( 'NG_ING_PREFIX', 'NGING' );
define( 'NG_ING_VERSION', '0.1.0' );
define( 'NG_ING_FOLDER', __DIR__ );
define( 'NGING_DELETE_ATTEMPTS', 5 );
$rules = json_decode( file_get_contents( __DIR__ . '/rules.json' ), true );
if ( ! $rules || ! is_array( $rules ) ) {
	throw new Exception( 'Invalid rules.json' );
}
define( 'NGING_REGEX_RULES', $rules );
require_once 'config.php';
$bot = new \Ng\Ingus\Controller\Bot();
$bot->get_updates( array() );
$bot->filter_updates( NGINS_CHATTS );
$cache = new \Ng\Ingus\Controller\Cache( 'cache.json' );
if ( empty( $bot->data['updates'] ) ) {
	exit;
}
shuffle( $bot->data['updates'] );
foreach ( $bot->data['updates'] as $update ) {
	$update_id = $update->getUpdateId();
	if ( $cache->get( NG_ING_PREFIX . '_update_' . $update_id ) ) {
		continue;
	}
	$guard = new \Ng\Ingus\Controller\Guard( $update );
	if ( defined( 'NGINS_COPY_ALL' ) && NGINS_COPY_ALL ) {
		$bot->send_message( NGINS_ADMIN_CHAT, 'Update: ' . $update->getMessage()->getText() . ' from ' . $update->getMessage()->getChat()->getId() . ' chat name:' . $update->getMessage()->getChat()->getTitle() );

	}
	if ( $guard->is_spam( $update ) ) {
		$try = (int) $cache->get( NG_ING_PREFIX . '_try_' . $update_id );
		if ( NGING_DELETE_ATTEMPTS < $try ) {
			$bot->send_message( NGINS_ADMIN_CHAT, 'Spam detected: ' . $update->getMessage()->getText() . ' but could not be deleted' );
			continue;
		}
		if ( 0 === $try ) {
			$bot->send_message( $update->getMessage()->getChat()->getId(), 'Spam detected, message will be deleted' );
		}
		++$try;
		$cache->set( NG_ING_PREFIX . '_try_' . $update_id, $try, 24 * 60 * 60 );
		$bot->delete_message( $update->getMessage()->getChat()->getId(), $update->getMessage()->getMessageId() );
	}
	$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
}
