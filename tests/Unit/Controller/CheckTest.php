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

	public function test_rules() {
		$this->assertIsArray( NGING_REGEX_RULES );
		$this->assertNotEmpty( NGING_REGEX_RULES );
	}
	public function test_emoji_true() {
		$text = '😊😊asdfdsafdsa😊😊😊😊😊';
		$this->assertTrue( $this->check_emoji( $text ) );
	}

	public function test_emoji_false() {
		$text = '😊😊😊 😊 Проверка, тут должно быть меньше 15% эмодзи';
		$this->assertFalse( $this->check_emoji( $text ) );
	}

	public function test_mixed_letters_true() {
		$text = 'Набираем пaртнёров, нeплохой зарaботок в нeделю,быстрое обучение, писaть в личныe соoбщения';
		$this->assertTrue( $this->check_mixed_letters( $text ) );
	}

	public function test_mixed_letters_true_false() {
		$texts_false = array(
			'Набираем партнёров, неплохой заработок в неделю,быстрое обучение, писать в личные сообщения',
		);
		$texts_true  = array(
			'🇷🇺ТРЕБУЮТСЯ ПАРТНЕРЫ мужчuны/женщuны
В ОНЛАЙН ПРОЕКТ🇷🇺

✔️ в удобном для вас режиме
✔️ можно совмещать
✔️ возраст от 22 лет
✔️ возможность работать из любой страны

💰предлαгαеᴍ:
✔️ 40 000 р.у.б. в нeдeлю.
✔️ грαфиᴋ 3-4 чαса в дeнь.
✔️ бeз σпытα 


∏иши, вᴄё рαᴄскажeм ➡️@kostin1013',
		);
		foreach ( $texts_true as $text ) {
			$this->assertTrue( $this->check_mixed_letters( $text ), 'tried ' . $text );
		}
		foreach ( $texts_false as $text ) {
			$this->assertFalse( $this->check_mixed_letters( $text ), 'tried ' . $text );
		}
	}

	public function test_regex_true() {
		$texts = array(

			'Нужны люди для удаленной работы
Сфера не сложная, но прибыльная
Доход каждый день от 120-180$ белый вид заработка 
В день до двух часов( при желании можно больше)  
УДАЛЕНКА!

Пишите "+" в лс',
			'Удаленка',
			'Пишите + в лс',
		);
		foreach ( $texts as $text ) {
			$error = 'got: ' . $text . "\n normalized: " . $this->normalize( $text ) . "\n";
			$this->assertTrue( $this->check_regex( $text ), $error );

		}
	}

	/*
		public function test_normalize(){
		$text = 'Набираем партнёров, неплохой заработок в неделю,быстрое обучение, писать в личные сообщения';
		$normalized = $this->normalize($text);
		$this->assertEquals('набираем партнёров неплохой заработок в неделю быстрое обучение писать в личные сообщения', $normalized);
		$text = 'Пишите "+" в лс';
		$normalized = $this->normalize($text);
		echo $text;
		$this->assertEquals('пишите + в лс', $normalized);
	}*/
}
