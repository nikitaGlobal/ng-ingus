<?php
/**
 * Main app file.
 */
require_once 'vendor/autoload.php';
date_default_timezone_set( 'Europe/Moscow' );
define( 'NG_ING_PREFIX', 'NGING' );
define( 'NG_ING_VERSION', '0.1.0' );
define( 'NGING_LOGFILE', __DIR__ . '/logs/ingus' . date( 'Ymd' ) . '.log' );
require_once 'config.php';
require_once 'rules.php';
$bot = new \Ng\Ingus\Controller\Bot();
$bot->get_updates( array() );
$bot->filter_updates( NGINS_CHATTS );
$cache = new \Ng\Ingus\Controller\Cache();
if ( empty( $bot->data['updates'] ) ) {
	exit;
}
foreach ( $bot->data['updates'] as $update ) {
	$update_id = $update->getUpdateId();
	if ( $cache->get( NG_ING_PREFIX . '_update_' . $update_id ) ) {
		echo '... skipping update ' . $update_id . PHP_EOL;
		continue;
	}
	$guard = new \Ng\Ingus\Controller\Guard( $update );
	if ( defined( 'NGINS_COPY_ALL' ) && NGINS_COPY_ALL ) {
		$bot->send_message( NGINS_ADMIN_CHAT, 'Update: ' .  $update->getMessage()->getText()  );

	}
	if ( $guard->is_spam( $update ) ) {
		// $bot->delete_message( $update->getMessage()->getChat()->getId(), $update->getMessage()->getMessageId() );
		$bot->send_message( NGINS_ADMIN_CHAT, 'Spam detected: ' . $update->getMessage()->getText() );
	}
	$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24*60*60 );
}
