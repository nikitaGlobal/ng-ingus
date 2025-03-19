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

	public function test_all() {
		$methods     = get_class_methods( 'Ng\Ingus\Controller\Check' );
		$methods     = array_filter(
			$methods,
			function ( $method ) {
				return 0 === strpos( $method, 'check_' );
			}
		);
		$texts_false = array(
			'Продам стульчик детский трансформер -30 лев , самовывоз из г.Обзор',
			'Соседи, прошу прощения. Бот еще только обучается, у него бывают ложные срабатывания. 
Если в сообщении эмодзи > 15%, это признак спамма.
Также он ошибочно воспринимал прикрепленные картинки как спамм, это я убрал. 
Я всем в личку написал и все решили, если кого пропустил, напишите мне, пожалуйста, я поправлю.
Всем хорошего дня!',
			'Продается ламинат, примерно 70 кв.м.  Б/У, в течение 15 лет использовался только в летний сезон. Доски  122*20, часть подрезаны под комнаты.
50 лев',
			'Продаётся абонемент в Аквахаус на 8 посещений (первоначально 10, использовано 2). Действует до 29.03.2025. Первоначальная стоимость оставшихся посещений — 300 лев, но предлагается скидка 10%, поэтому цена — 270 лев.
информация о SPA на сайте: https://ensanahotels.com/bg/hotels/aquahouse/spa',
			'А получать на почту легко. Мне и Енерго-Про, и ВиК присылают, ничего особенного для этого вроде не делал, просто указал email при заключении договора с ними.
Виваком тоже. Вообще бумажных счетов никогда не получал за 8.5 лет, т.е. у них это уже довольно давно организовано.

Ну и epay.bg тоже умеет уведомлять на почту.',
			'',
		);
		$texts_true  = array(
			'В поисках охраны-от 8000 руб. в день.
Также нужен водитель с личным/арендным авто и без. 
 Оплата от 25 500 руб. за один рейс!  Расходы в дороге компенсируем.',
		);
		foreach ( $texts_true as $text ) {
			$results = array();
			foreach ( $methods as $method ) {
				$results[ $method ] = $this->$method( $text );
			}
			print_r( $results );
			$this->assertTrue( in_array( true, $results ), 'should be spam. Tried ' . $text );
		}
		foreach ( $methods as $method ) {
			foreach ( $texts_false as $text ) {
				$this->assertFalse( $this->$method( $text ), 'Should be fine. Tried ' . $text );

			}
		}
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
