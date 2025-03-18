<?php
declare(strict_types=1);
namespace Ng\Ingus\Tests\Controller;

use Ng\Ingus\Controller\Check;
use PHPUnit\Framework\TestCase;

define(
	'NGING_REGEX_RULES',
	json_decode(
		file_get_contents( dirname( __DIR__, 3 ) . '/rules/rules.json' ),
		true
	)
);

class CheckTest extends TestCase {

	use Check;

	public function test_emoji_true() {
		$text = '😊😊asdfdsafdsa😊😊😊';
		$this->assertTrue( $this->check_emoji( $text ) );
	}

	public function test_emoji_false() {
		$text = '😊😊😊 😊';
		$this->assertFalse( $this->check_emoji( $text ) );
	}

	public function test_mixed_letters_true() {
		$text = 'Набираем пaртнёров, нeплохой зарaботок в нeделю,быстрое обучение, писaть в личныe соoбщения';
		$this->assertTrue( $this->check_mixed_letters( $text ) );
	}

	public function test_mixed_letters_false() {
		$text = 'Набираем партнёров, неплохой заработок в неделю,быстрое обучение, писать в личные сообщения';
		$this->assertFalse( $this->check_mixed_letters( $text ) );
	}
}
