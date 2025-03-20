<?php
/**
 * Main app file.
 */
require_once 'vendor/autoload.php';
date_default_timezone_set( 'Europe/Moscow' );
define( 'NG_ING_PREFIX', 'NGING' );
define( 'NG_ING_VERSION', '0.1.0' );
define( 'NG_ING_FOLDER', __DIR__ );
require_once 'config.php';
global $bot;
$bot = new \Ng\Ingus\Controller\Bot();
$bot->get_updates( array() );
global $cache;
$cache = new \Ng\Ingus\Controller\Cache( 'cache.json' );
if ( ! empty( $argv ) ) {
	foreach ( $argv as $arg ) {
		define( 'NGINS_ARGV_' . str_replace( '-', '', strtoupper( $arg ) ), true );
	}
}
$rules    = json_decode( file_get_contents( __DIR__ . '/rules/rules.json' ), true );
$cas_chat = new \Ng\Ingus\Controller\Cas_Chat( $cache );
if ( ! $rules || ! is_array( $rules ) ) {
	send_message( NGINS_ADMIN_CHAT, 'Invalid rules.json' );
	throw new Exception( 'Invalid rules.json' );
}
define( 'NGING_REGEX_RULES', $rules );
if ( empty( $bot->data['updates'] ) ) {
	exit;
}

// shuffle( $bot->data['updates'] );
foreach ( $bot->data['updates'] as $update ) {
	// if user joined group
	$update_id = $update->getUpdateId();
	if ( $cache->get( NG_ING_PREFIX . '_update_' . $update_id ) ) {
		continue;
	}
	if ( $update->getMessage()->getNewChatMembers() && $cas_chat->check_user( $update->getMessage()->getFrom()->getId() ) ) {
		send_message( NGINS_ADMIN_CHAT, 'User is banned in cas.chat, muted' . "\n" . $update_info );
		$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
		restrict_user( $update );
		continue;
	}
	if ( $update->getMessage()->getLeftChatMember() ) {
		continue;
	}
	echo 'checking ' . $update_id . PHP_EOL;
	$update_info = $bot->get_update_info( $update );
	$guard       = new \Ng\Ingus\Controller\Guard( $update );
	if ( $cas_chat->check_user( $update->getMessage()->getFrom()->getId() ) ) {
		send_message( NGINS_ADMIN_CHAT, 'User is banned in cas.chat' . "\n" . $update_info );
		$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
		restrict_user( $update );
	}
	if ( $guard->is_spam( $update ) ) {
		send_message( NGINS_ADMIN_CHAT, 'Spam detected, message will be deleted' . "\n" . $update_info );
		$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
		restrict_user( $update );
		continue;
	}
	if ( $cas_chat->check_user( $update->getMessage()->getFrom()->getId() ) ) {
		send_message( NGINS_ADMIN_CHAT, 'User is banned in cas.chat' . "\n" . $update_info );
		$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
		restrict_user( $update );
	}
	if ( defined( 'NGINS_COPY_ALL' ) && NGINS_COPY_ALL ) {
		$cache->set( NG_ING_PREFIX . '_update_' . $update_id, $update_id, 24 * 60 * 60 );
		send_message( NGINS_ADMIN_CHAT, 'New message: ' . "\n" . $update_info );
	}
}

function send_message( $chat_id, $text ) {
	global $cache;
	$action = 'send_message' . serialize( array( $chat_id, $text ) );
	if ( $cache->get( $action ) ) {
		return;
	}
	$cache->set( $action, 1, 300 );
	global $bot;
	$bot->send_message( $chat_id, $text );
}
function restrict_user( $update ) {
	if ( defined( 'NGINS_ARGV_DRYRUN' ) && NGINS_ARGV_DRYRUN ) {
		echo 'dry run' . PHP_EOL;
		return;
	}
	global $bot;
	$bot->restrict_chat_member( $update->getMessage()->getChat()->getId(), $update->getMessage()->getFrom()->getId() );
	$bot->delete_message( $update->getMessage()->getChat()->getId(), (int) $update->getMessage()->getMessageId() );
}
