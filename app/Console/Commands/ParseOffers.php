<?php

namespace App\Console\Commands;

use App\Jobs\OfferParser;
use Illuminate\Console\Command;

class ParseOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг предложений из файла';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		// Берем из файла список офферов
        $content = file_get_contents(public_path('offers-min.html'));
		// Разбиваем его на части
		$offers = $this->parse($content, 'all', ' id="card-');
		
		// Каждый оффер отправляем на парсинг
		for ($i = 1; $i < count($offers); $i++) {
			OfferParser::dispatch($offers[$i]);
		}
    }
	
	/**
	* Парсит строку между divider1 и divider2
	* $key - номер (от 0) части для обработки
	* Если $key = 'all', то возвращается массив
	*/
	private function parse($content, $key, $divider1, $divider2 = false)
	{
		if ($divider1) {
			$parts = explode($divider1, $content);
			
			// all parts return
			if ($key == 'all') {
				return $parts;
			}
			
			$content = $parts[$key];
		}
		
		if ($divider2) {
			list($content, ) = explode($divider2, $content);
		}
		
		return trim($content);
	}
}
