$(function(){

	$(document).on('click','.approval_button',function(){
		$('#formModal .modal-body').html('<div class="approval_modal"><div class="text-center"><i class="fa fa-thumbs-o-up"></i></div><p>Данная шкала показывает примерную вероятность одобрения вашей заявки в банке или МФК/МКК. Рассчитывается с помощью нескольких внутренних параметров в диапазоне от 0 до 100%.</p></div>');
        $('#formModal').modal();
	});

	$(document).on('click','.k5m_button',function(){
		$('#formModal .modal-body').html('<div class="k5m_modal"><div class="text-center"><img src="/vzo_theme/img/popup1.png" alt=""></div><p>К5М® — рейтинг, с помощью которого мы оцениваем финансовые продукты (например, микрозаймы, кредиты или кредитные карты). Для объективной оценки используется сложная формула, которая учитывает большое число параметров (в сентябре 2016 года их было всего 5 штук, а через два года — уже более 80). В процессе оценки задействованы не только основные параметры продукта (такие как сумма или процентная ставка кредита), но и оценки клиентов.</p><div class="text-center"><a class="form-btn1" target="_blank" rel="nofollow" href="/about">Подробнее</a></div></div>');
        $('#formModal').modal();
	});

	$(document).on('click','.informer_button',function(){
		$('#formModal .modal-body').html('<div class="k5m_modal"><div class="text-center"><img src="/vzo_theme/img/informer_600.png" alt=""></div><p>С помощью кредитного рейтинга вы можете моментально узнать свое «финансовое здоровье», которое представлено в виде числового показателя в районе 300-850 баллов. Главное преимущество — вы получите информацию, заполнив лишь небольшую форму, в режиме онлайн совершенно бесплатно. Без платы за подписку и навязчивой рекламы.</p><br><div class="text-center"><div class="video-responsive" id="loadVideo"><iframe width="auto" height="auto" src="https://www.youtube.com/embed/Xbf2JWv2KA0" frameborder="0" allowfullscreen=""></iframe></div><a class="form-btn1" target="_blank" rel="nofollow" href="/get-rating">Проверить бесплатно</a></div></div>');
        $('#formModal').modal();
	});

	$(document).on('click','.about_vzo',function(){
		$('#formModal .modal-body').html('<div class="k5m_modal"><div class="text-center"><div class="video-responsive" id="loadVideo"><iframe width="auto" height="auto" src="https://www.youtube.com/embed/WlUeJhXaUzI" frameborder="0" allowfullscreen=""></iframe></div></div></div>');
		$('#formModal').modal();
	});

});
