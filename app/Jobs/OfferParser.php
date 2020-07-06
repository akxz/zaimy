<?php

namespace App\Jobs;

use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OfferParser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	protected $str;
	
	private $hashes = [
		'e9ccc9d1f194bbe3617468a85578d864' => 'op1',
		'648b79a675080ca4d17cd330932fad7d' => 'op2',
		'3f7aa529dfa21bd6ae6a0fe094d826ce' => 'op3',
		'7455cc8c57e595f4d0e09a3e13b3dc2d' => 'op4',
		'6be02bd322a08587ad4f8bb09325b7f0' => 'op5',
		'61de5b69f1261761de1209a00f03c207' => 'op6',
		'318f3595a826a6fd34c9b2ae52832a5c' => 'op7',
		'72f4683dc19836ed91eef2f7cde18891' => 'op8',
		'8c2de96d25d93f35e6565106c620375e' => 'op9',
		'49a557c4d56e7464495596a9bfd059c9' => 'op10',
		'5ee80894d249ec5e512cab2f2c8f3af9' => 'op11',
		'dc9344c150667f8bb5108e02025e4659' => 'op12',
		'c94f076100c9d512919cc7973472f5f3' => 'op13',
		'3bf99785e1208a28e2ccc1f7e278bf48' => 'op14',
		'bf0b46d51325db2f8f7115b42298809d' => 'op15',
		'8f097fd572d5e0054ee79a1eba4a5cb8' => 'op16',
		'8c7ef6e9d43634baf1eff90b6de4a968' => 'op17',
		'24c68006b82143d7d899cb6d4593d792' => 'op18',
		'3bbcca2b9027c62ae072718e515416ef' => 'op19',
		'1aa1cdcaaa0a11bd453c0c8eefa848f0' => 'op20',
		'1b05cabc978e0f1430f2c9631ff82fc5' => 'op21',
		'909dbc3e1c8dac87177007316daee100' => 'op22',
		'254887a76db706f4364ff264745587e4' => 'op23',
		'c8c413b245379f1ee49e7a33d18edab5' => 'op24',
		'e01d86f3072d3323746aea6d76510a06' => 'op25',
		'259525674ac72e267164e69fa0855ede' => 'op26',
		'0ecb60d48034fe9f46faa88fb20b49a8' => 'op27',
		'e5a44dceb682740f2e317745b4a8b74a' => 'pm1',
		'335cd444e404b49d68e5c3ec03f9d43a' => 'pm2',
		'f47c88bbcc7cb268c524703d7e6f4526' => 'pm3',
		'7a4b8c97586cd1566865e277cc3701b6' => 'pm4',
		'1600f3f49c017ae85e2d9a9003e6a98e' => 'pm5',
		'c5488eebd1e3a95452005fa6e436c3db' => 'pm6',
		'85abfb8ac8056eb5824f1b91b564e9d8' => 'pm7',
		'84126e8b184a578188c4430881655a4c' => 'pm8',
		'275fb39aa45840b7ecc1201abfa7d8c5' => 'pm9',
		'ff03fc015732d0f040d4d135ec774c96' => 'pm10',
		'1e37055cb7ccaf8eea632df45f4e88ae' => 'pm11',
		'62e8de299524eef36f8acea9c69b4500' => 'pm12',
		'3cb80a33fce546153ceabc9c55166c73' => 'pm13',
		'7af107dbbbd19f02b7c5bc9d92eb40fc' => 'pm14',
		'9571b55716d80a47943f004fdec3d15a' => 'pm15',
		'7268b155103c164166c1caaa46b9f5bf' => 'pm16',
		'7dee5d7179700fde86575dd33f2d776a' => 'pm17',
		'ba1a3b23090dd3422ee2fcbd4395d412' => 'pm18',
	];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($str)
    {
        $this->str = $str;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $str = $this->str;
		
		$offer = new Offer;
		$offer->card_id = explode('"', $str)[0];
		
		$name = $this->parse($str, 1, 'org-card', '</a>');
		$offer->name = explode('>', $name)[1];
		
		$offer->k5m = $this->parse($str, 1, 'К5М = ', '/');
		
		$offer->min_credit_rating = $this->parse($str, 1, '<div class="informer__value">', '<img');			
		
		if (strpos($str, '/100%</div>') !== false) {
			$approval = $this->parse($str, 0, '/100%</div>');
			$approval = explode('>', $approval[0]);
			$offer->approval = trim(array_reverse($approval)[0]);
		}	
		
		if (strpos($str, '/5</div>') !== false) {
			$reviews_rating = $this->parse($str, 0, '/5</div>');
			$reviews_rating = explode('>', $reviews_rating[0]);
			$offer->reviews_rating = trim(array_reverse($reviews_rating)[0]);
		}
		
		if (strpos($str, ' отзыв') !== false) {
			$reviews_count = $this->parse($str, 0, ' отзыв');
			$reviews_count = explode('>', $reviews_count[0]);
			$offer->reviews_count = trim(array_reverse($reviews_count)[0]);
		}
		
		if (strpos($str, '/vzo_theme/img/stars/') !== false) {
			$offer->reviews_icon = $this->parse($str, 1, '/vzo_theme/img/stars/', '&');
		}
		
		$offer->logo = $this->parse($str, 1, '/images/zajm/', '"');
		$offer->description = $this->parse($str, 1, '<div class="accordion-p">', '</div>');
		
		$offer->info_list = $this->parse_info_list($str);
		
		$icons = $this->parse($str, 1, '<div class="vzo_icons_wrap">', '/div');
		$offer->option_icons = $this->parse_icons($icons, '/images/ic/');
		
		$files = $this->parse($str, 'all', '/files/');
		$offer->files = $this->parse_files($files);
		
		// Ячейки таблицы
		$lvc = $this->parse($str, 'all', 'class="lvc ');
		
		for ($i = 1; $i < count($lvc); $i++) {
			$value = $this->parse_lvc($lvc[$i]);
			
			if (strpos($lvc[$i], 'Сумма:') !== false) {
				$amount = $this->parse_money($value);
				$offer->amount_min = $amount['min'];
				$offer->amount_max = $amount['max'];
			} elseif (strpos($lvc[$i], 'Срок:') !== false) {
				$terms = $this->parse_terms($value);
				$offer->term_min = $terms['min'];
				$offer->term_max = $terms['max'];
			} elseif (strpos($lvc[$i], 'Ставка в день:') !== false) {
				$offer['daily_rate'] = str_replace('%', '', $value);
			} elseif (strpos($lvc[$i], 'Переплата, от:') !== false) {
				$offer['overpayment'] = explode(' ', $value)[0];
			} elseif (strpos($lvc[$i], 'Возраст:') !== false) {
				$age = $this->parse_age($value);
				$offer->age_min = $age['min'];
				$offer->age_max = $age['max'];
			} elseif (strpos($lvc[$i], 'Способ выплаты:') !== false) {
				$offer->payment_methods = $this->parse_icons($lvc[$i], '/images/ic_pay/');
			} elseif (strpos($lvc[$i], 'Способ погашения:') !== false) {
				$offer->repayment_methods = $this->parse_icons($lvc[$i], '/images/ic_pay/');
			} elseif (strpos($lvc[$i], 'Документы:') !== false) {
				$offer->docs = $value;
			} elseif (strpos($lvc[$i], 'Скорость рассмотрения заявки:') !== false) {
				$offer->processing_speed = $value;
			} elseif (strpos($lvc[$i], 'Скорость выплаты:') !== false) {
				$offer->payout_speed = $value;
			} elseif (strpos($lvc[$i], 'График работы:') !== false) {
				$offer->schedule = $value;
			} elseif (strpos($lvc[$i], 'Плохая КИ:') !== false) {
				$offer->bad_history = $value;
			} elseif (strpos($lvc[$i], 'Продление:') !== false) {
				$offer->prolong = $value;
			} elseif (strpos($lvc[$i], 'Начало работы:') !== false) {
				$offer->year = (int) $value;
			} elseif (strpos($lvc[$i], 'Идентификация:') !== false) {
				$offer->identification = $value;
			} elseif (strpos($lvc[$i], 'Инвесторам:') !== false) {
				$offer->investors = $value;
			}
		}
		
		$offer->save();
    }
	
	/**
	* Парсит строку между divider1 и divider2
	* $key - номер (от 0) части для обработки
	* Если $key = 'all', то возвращается массив
	*/
	private function parse($content, $key, $divider1 = false, $divider2 = false)
	{
		if ($divider1) {
			$parts = explode($divider1, $content);
			
			// all parts return
			if ($key == 'all') {
				return $parts;
			}
			
			$content = isset($parts[$key]) ? $parts[$key] : '';
		}
		
		if ($divider2) {
			list($content, ) = explode($divider2, $content);
		}
		
		return trim($content);
	}
	
	// Получает ячейки таблицы для оффера
	private function parse_lvc($content)
	{
		$value = $this->parse($content, 1, 'class="value', '</div>');
		list(, $value) = explode('>', $value);
		return trim($value);
	}
	
	// Парсит сумму
	private function parse_money($content)
	{
		$min = $this->parse($content, 1, 'от', 'до');
		$min = (int) str_replace(' ', '', $min);
		$max = $this->parse($content, 1, 'до', 'руб');
		$max = (int) str_replace(' ', '', $max);
		
		return ['min' => $min, 'max' => $max];
	}
	
	// Сроки погашения
	private function parse_terms($content)
	{
		$min = $this->parse($content, 1, 'от', 'до');
		$min = (int) str_replace(' ', '', $min);
		$max = $this->parse($content, 1, 'до', 'д');
		$max = (int) str_replace(' ', '', $max);
		
		return ['min' => $min, 'max' => $max];
	}
	
	// Мин/макс возраст
	private function parse_age($content)
	{
		if (strpos($content, 'год') !== false) {
			$divider3 = 'год';
		} else {
			$divider3 = 'лет';
		}
		
		if (strpos($content, 'до') !== false) {
			$min = $this->parse($content, 1, 'от', 'до');
			$min = (int) str_replace(' ', '', $min);
			$max = $this->parse($content, 1, 'до', $divider3);
			$max = (int) str_replace(' ', '', $max);
		} else {
			$min = $this->parse($content, 1, 'от', $divider3);
			$min = (int) str_replace(' ', '', $min);
			$max = null;
		}
		
		return ['min' => $min, 'max' => $max];
	}
	
	private function parse_info_list($str)
	{
		$str = $this->parse($str, 1, '<ul class="lv-list">', '</ul>');
		$str = str_replace('</li>', '', $str);
		$parts = $this->parse($str, 'all', '<li>');
		$return = [];
		
		for ($i = 1; $i < count($parts); $i++) {
			$return[] = trim($parts[$i]);
		}
		
		return json_encode($return);
	}
	
	private function parse_icons($content, $divider)
	{
		$return = [];
		$hashes = $this->hashes;
		$images = $this->parse($content, 'all', $divider);
		
		for ($i = 1; $i < count($images); $i++) {
			$image = explode('&', $images[$i])[0];
			$hash = md5($divider . $image);
			$return[] = $hashes[$hash];
		}
		
		return json_encode($return);
	}
	
	private function parse_files($files)
	{
		$return = [];
		
		for ($i = 1; $i < count($files); $i++) {
			$return = array_merge(
				$return,
				[
					[
						'url' => explode('"', $files[$i])[0],
						'title' => $this->parse($files[$i], 1, 'target="_blank">', '</a>')
					]
				]
			);
		}
		
		return json_encode($return);
	}
}
