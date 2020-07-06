<?php

namespace App\Traits;

trait OffersTrait
{
	private $titles = [
		'op1' => 'Компания предусматривает специальные условия для клиентов с плохой кредитной историей',
		'op2' => 'Компания принимает и рассматривает заявки на займы в круглосуточном режиме',
		'op3' => 'Займ в этой компании можно продлить за уплату начисленных процентов или комиссии',
		'op4' => 'Займ погашается частями - каждую неделю, каждый месяц или каждые полмесяца',
		'op5' => 'Данная компания зарегистрирована Центробанком как микрокредитная',
		'op6' => 'Для получения данного кредита достаточно только паспорта',
		'op7' => 'Для оформления этого кредита не требуется залог имущества',
		'op8' => 'Для оформления этого кредита не требуется поручительство',
		'op9' => 'Заявки на займы рассматриваются в ручном режиме',
		'op10' => 'Оформить заявку и получить займ можно в удаленном режиме',
		'op11' => 'Компания может передавать данные заемщиков третьим лицам',
		'op12' => 'Проблемным заемщикам доступна программа исправления кредитной истории',
		'op13' => 'Компания работает по всей России, в том числе в Крыму и отдаленных регионах',
		'op14' => 'Займ в компании можно погасить досрочно - как полностью, так и частично',
		'op15' => 'Всем новым клиентам компании доступен беспроцентный займ',
		'op16' => 'Займ в этой компании оформляется и перечисляется в течение нескольких минут',
		'op17' => 'Данная компания зарегистрирована Центробанком как микрофинансовая',
		'op18' => 'Кредит можно оформить с выдачей наличными без комиссии',
		'op19' => 'Оформить заявку и получить займ можно в одном из отделений компании',
		'op20' => 'Эта МФК принимает инвестиции от физических и юридических лиц',
		'op21' => 'Банк снижает ставку по кредиту при выполнении определенных условий',
		'op22' => 'Компания защищает данные заемщиков и не передает их третьим лицам',
		'op23' => 'Заявки на займы рассматриваются в автоматическом режиме',
		'op24' => 'Для заемщиков доступно мобильное приложение компании на iOS и Android',
		'op25' => 'Банк может потребовать наличия поручителя при оформлении кредита',
		'op26' => 'Кредит можно оформить для погашения задолженностей по другим кредитам',
		'op27' => 'Банк может потребовать залог недвижимости при оформлении кредита',
		'pm1' => 'На банковскую карту',
		'pm2' => 'Наличными',
		'pm3' => 'На банковский счет',
		'pm4' => 'На QIWI',
		'pm5' => 'Через Золотую Корону',
		'pm6' => 'На Яндекс.Деньги',
		'pm7' => 'В платежных терминалах',
		'pm8' => 'Через Contact',
		'pm9' => 'Через Связной',
		'pm10' => 'Через Элекснет',
		'pm11' => 'Через Почту России',
		'pm12' => 'С помощью СМС',
		'pm13' => 'Через Евросеть',
		'pm14' => 'Через CyberPlat',
		'pm15' => 'Через МТС',
		'pm16' => 'Через Билайн',
		'pm17' => 'Через Рапиду',
		'pm18' => 'Через Юнистрим',
	];
	
	public function show_offers($data)
	{
		$offers = $data['offers'];
		$link = $data['link'];
		$out = '';
		
		foreach ($offers as $offer) {
			$out .= $this->offer($offer, $link);
		}
		//$return = $this->offer($data);
		
		return $out;
	}
	
	private function offer($offer, $link)
	{
		if ($offer->is_specoffer == 1) {
			$specoffer_class = 'spec-offer';
			$specoffer_title = '<div class="title-top"><i class="fa fa-star-o"></i> Специальное предложение</div>';
		} else {
			$specoffer_class = '';
			$specoffer_title = '';
		}
		
		$titles = $this->titles;
		$option_icons = json_decode($offer->option_icons);
		$option_icons_html = '';
		
		foreach ($option_icons as $icon) {
			$option_icons_html .= '<span class="sprite vzo_icons def_bg bg_' . $icon . '" data-title="' . $titles[$icon] . '"></span>';
		}
		
		$payment_icons = json_decode($offer->payment_methods);
		$payment_icons_html = '';
		
		foreach ($payment_icons as $icon) {
			$payment_icons_html .= '<span class="zaim-p-icon def_bg bg_' . $icon . '" data-title="' . $titles[$icon] . '"></span>';
		}
		
		$repayment_icons = json_decode($offer->repayment_methods);
		$repayment_icons_html = '';
		
		foreach ($repayment_icons as $icon) {
			$repayment_icons_html .= '<span class="zaim-p-icon def_bg bg_' . $icon . '" data-title="' . $titles[$icon] . '"></span>';
		}
		
		$list_items = json_decode($offer->info_list);
		$info_list_items = '';
		
		foreach ($list_items as $item) {
			$info_list_items .= '<li>' . $item . '</li>';
		}
		
		$files_list = json_decode($offer->files, TRUE);
		$files = '';
		
		if (is_array($files_list)) {
			if (! empty($files_list)) {
				foreach ($files_list as $item) {
					$files .= '<li><i class="fa fa-download"></i> <a href="/files/' . $item['url'] . '" target="_blank">' . $item['title'] . '</a></li>';
				}
			}
		}
		
		$amount_max = number_format($offer->amount_max, 0, '', ' ') . ' руб.';
		$amount = 'от ' . number_format($offer->amount_min, 0, '', ' ') . ' до ' .number_format($offer->amount_max, 0, '', ' ') . ' руб.';
		
		$terms = 'от ' . number_format($offer->term_min, 0, '', ' ') . '
						до ' . number_format($offer->term_max, 0, '', ' ') . ' дней';
		
		$age_max = (is_null($offer->age_max)) ? '' : 'до ' . $offer->age_max;
							
		
		$return = <<<EOT
		
		<div id="card- $offer->card_id" class="one-offer $specoffer_class">
			$specoffer_title
			<div class="row mob-relative">
				<div class="col-md-4 mob-mar">
					<div class="bor">
						<span class="cart-number"> $offer->id </span>
						<img loading="lazy" src="/images/zajm/$offer->logo" alt="$offer->name">
					</div>
					<div class="bor">
						<div class="approval-line no-print">
							<div class="tt">Одобрение <img loading="lazy" src="/vzo_theme/img/icon-help2.png" alt="Одобрение" class="icon-help approval_button">
							</div>
							<div class="progressbar">
								<div class="progress progress-bar bg-success progress-bar-striped" style="width: 65%"></div>
							</div>
						</div>
					</div>
					<div class="bor">
						<div class="tt">К5М =  $offer->k5m /10 <img loading="lazy" src="/vzo_theme/img/icon-help2.png" alt="Рейтинг К5М" class="icon-help k5m_button">
						</div>
					</div>
					<div class="bor informer no-print">
						<div class="informer__title">Требуемый кредитный рейтинг</div>
						<div class="informer__content">
							<img loading="lazy" src="/vzo_theme/img/informer_600.png" alt="">
							<div class="informer__value">
								600 <img loading="lazy" src="/vzo_theme/img/icon-help2.png" alt="Требуемый кредитный рейтинг" class="informer__value-icon informer_button">
							</div>
						</div>
					</div>
																																		<a data-id="$offer->card_id" href="$offer->url" target="_blank" class="hdl form-btn1 no-print offer"> <i class="fa fa-lock"></i> Оформить</a>
																																		<button data-id="$offer->card_id" class="compare add_to_compare"><span>+ к сравнению</span></button>
						<span data-id="$offer->card_id" class="complaint"><i class="fa fa-warning"></i> <span>Подать жалобу</span></span>
				</div>
				<div class="col-md-8 mob-initial">
					<div class="top-cart">
						<div class="name-line"><a class="org-card" target="_blank" href="$link">
						 $offer->name </a></div>
						<div class="rating-line">
							<span class="star_span def_bg" style="background: url('/vzo_theme/img/stars/$offer->reviews_icon ');"></span>
							<div class="text-rating">
								<a target="_blank" href="$link"> $offer->reviews_count   отзывов</a>
								<span class="val-rating">($offer->reviews_rating  из 5)</span>
							</div>
						</div>
						<div class="row three-block">
							<div class="col-md-4">
								<i class="fa fa-check-circle"></i><span>$amount_max</span>
							</div>
							<div class="col-md-4">
								<i class="fa fa-check-circle"></i><span> $offer->term_max  дней</span>
							</div>
							<div class="col-md-4">
								<i class="fa fa-check-circle"></i><span> $offer->daily_rate  % в день</span>
							</div>
						</div>
						<div class="refresh-item">
							<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i> <span>Обновлено</span><br><span>29.06.2020</span>
							</div>    
						</div>
						<div class="vzo_icons_wrap">
						
						$option_icons_html
						
						</div>
						<div class="row">
							<div class="col-md-6">
							 <div class="lvc fa-money">
						Сумма:
						<div class="value">$amount
						</div>
					</div>
					<div class="lvc fa-calendar">
						Срок:
						<div class="value">$terms
						</div>
					</div>
					<div class="lvc fa-percent">
						Ставка в день:
						<div class="value"> $offer->daily_rate %</div>
					</div>
					<div class="lvc fa-money">
					Переплата, от:
						<div class="value">
						 $offer->overpayment  руб.
						</div>
					</div>
					<div class="lvc fa-users">
						Возраст:
						<div class="value">от  $offer->age_min 
							$age_max
						</div>
					</div>
					<div class="lvc fa-plus-circle">
						Способ выплаты:
						<div class="value mob-block">
							$payment_icons_html
						</div>
					</div>
					<div class="lvc fa-share-square-o">
						Способ погашения:
						<div class="value mob-block">
							$repayment_icons_html
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="lvc fa-id-card-o">
						Документы:
						<div class="value"> $offer->docs </div>
					</div>
					<div class="lvc fa-bolt">
						Скорость рассмотрения заявки:
						<div class="value"> $offer->processing_speed </div>
					</div>
					<div class="lvc fa-tachometer">
						Скорость выплаты:
						<div class="value"> $offer->payout_speed </div>
					</div>
					<div class="lvc fa-id-badge">
						Идентификация:
						<div class="value"> $offer->identification </div>
					</div>
					<div class="lvc fa-user-secret">
						Плохая КИ:
						<div class="value"> $offer->bad_history </div>
					</div>
					<div class="lvc fa-clock-o">
						График работы:
						<div class="value"> $offer->schedule </div>
					</div>
					<div class="lvc fa-hourglass-half">
						Продление:
						<div class="value"> $offer->prolong </div>
					</div>
					<div class="lvc fa-briefcase">
						Инвесторам:
						<div class="value"> $offer->investors </div>
					</div>
					<div class="lvc fa-calendar-o">
						Начало работы:
						<div class="value"> $offer->year </div>
					</div>
				</div>
			</div>
			
			<ul class="lv-list">
			$info_list_items
			</ul>
		</div><!-- end col-md-6 -->
		</div><!-- end row -->
		<span class="cart_more no-print">Подробнее <i class="fa fa-angle-up"></i></span>
		<div class="panel-cart" style="display: none;">
			<hr class="accordion-hr">
			<div class="row spidometrs spd-1">
				<div class="col-md-4 col-sm-4">
					<span class="cpt">К5М</span>
					<div class="spidometr def_bg" style="background: url('/vzo_theme/img/scala.png');">
						<span style="transform: rotate(156.8deg); background: url('/vzo_theme/img/arrow_spidometr.png');" class="arrow-spidometr def_bg"></span>
					</div>
					<div class="val-rating"> $offer->k5m /10</div>
				</div>
				<div class="col-md-4 col-sm-4">
					<span class="cpt">Отзывы</span>
					<div class="spidometr def_bg" style="background: url('/vzo_theme/img/scala.png');">
						<span style="transform: rotate(115deg); background: url('/vzo_theme/img/arrow_spidometr.png');" class="arrow-spidometr def_bg"></span>
					</div>
					<div class="val-rating"> $offer->reviews_rating /5</div>
				</div>
				<div class="col-md-4 col-sm-4">
					<span class="cpt">Одобрение</span>
					<div class="spidometr def_bg" style="background: url('/vzo_theme/img/scala.png');">
						<span style="transform: rotate(117deg); background: url('/vzo_theme/img/arrow_spidometr.png');" class="arrow-spidometr def_bg"></span>
					</div>
					<div class="val-rating"> $offer->approval /100%</div>
				</div>
			</div>
			<div class="accordion-p"> $offer->description </div>
			<ul class="docs-list no-print">
				$files
			</ul>
			
			<div class="cl-list d-flex space-around no-print">
				<a href="#" class="print_card"><i class="fa fa-print"></i> Распечатать</a>
				<span>
				<a href="#" data-id="$offer->card_id" class="favorite add_to_favorite"><i class="fa fa-star"></i> <span class="fav">В избранное</span></a>
				</span>
				 <a href="$link" target="_blank"><i class="fa fa-phone"></i> Служба поддержки</a>                                  <a href="$link" target="_blank"><i class="fa fa-user"></i> Личный кабинет</a>
			</div>
		</div>
		</div>
		
		EOT;
		
		return $return;
	}
}
