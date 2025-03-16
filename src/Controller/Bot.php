<?php
declare( strict_types = 1 );
namespace Ng\Ingus\Controller;

use TelegramBot\Api\BotApi as Tapi;
class Bot {
	private $bot;
	public $data = array();
	/**
	 * Constructor.
	 */
	function __construct() {
		$this->bot = new Tapi( NGING_BOT_TOKEN );
	}
	public function send_message( $chat_id, $text ): self {
		$this->bot->sendMessage( $chat_id, $text );
		return $this;
	}

	public function reply_message( $chat_id, $text, $reply_to_message_id ): self {
		$this->bot->sendMessage( $chat_id, $text, null, false, null, null, null, $reply_to_message_id );
		return $this;
	}

	public function get_updates( $args = array() ) {
		$this->data['updates'] = $this->bot->getUpdates( $args );
		return $this;
	}

	public function filter_updates( $chats ) {
		$this->data['updates'] = array_filter(
			$this->data['updates'],
			function ( $update ) use ( $chats ) {
				if ( null === $update->getMessage() ) {
					return false;
				}
				return in_array( $update->getMessage()->getChat()->getId(), $chats );
			}
		);
		return $this;
	}

	public function delete_message( $chat_id, $message_id ): self {
		try {
			$this->bot->deleteMessage( $chat_id, $message_id );
		} catch ( Exception $e ) {
			trigger_error( 'Error deleting message: ' . $e->getMessage() );
		}
		return $this;
	}
}
