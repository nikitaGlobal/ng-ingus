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

	public function restrict_chat_member( $chat_id, $user_id ): self {
		try {
			$this->bot->restrictChatMember( $chat_id, $user_id, null, null, null, null, null, null );
		} catch ( Exception $e ) {
			trigger_error( 'Error restricting chat member: ' . $e->getMessage() );
		}
		return $this;
	}

	public function get_update_info( $update ) {
		if ( ! $update->getMessage() ) {
			return false;
		}
		$date      = $update->getMessage()->getDate();
		$update_id = $update->getUpdateId();
		$info      = array(
			'update_id'   => $update_id,
			'bot_version' => NG_ING_VERSION,
			'chat_id'     => $update->getMessage()->getChat()->getId(),
			'date'        => date( 'Y-m-d H:m:s', $date ),
			'text'        => $update->getMessage()->getText(),
			'from'        => $update->getMessage()->getFrom()->getId(),
			'sender_name' => $update->getMessage()->getFrom()->getFirstName() . ' ' . $update->getMessage()->getFrom()->getLastName() . ' @' . $update->getMessage()->getFrom()->getUsername(),
			'chat name'   => $update->getMessage()->getChat()->getTitle(),
		);
		// attach photos, videos and captions if has some.
		if ( $update->getMessage()->getPhoto() ) {
			$info['photo'] = $update->getMessage()->getPhoto();
		}
		if ( $update->getMessage()->getVideo() ) {
			$info['video'] = $update->getMessage()->getVideo();
		}
		// get image or video or gallery caption.
		if ( $update->getMessage()->getCaption() ) {
			$info['caption'] = $update->getMessage()->getCaption();
		}
		// if has gallery
		if ( $update->getMessage()->getMediaGroupId() ) {
			$info['media_group_id'] = $update->getMessage()->getMediaGroupId();
		}
		// if has file attachments.
		if ( $update->getMessage()->getDocument() ) {
			$info['document'] = $update->getMessage()->getDocument();
		}
		ob_start();
		print_r( $info );
		$contents = ob_get_contents();
		ob_end_clean();
		return '```' . PHP_EOL . $contents . PHP_EOL . '```';
	}
}
