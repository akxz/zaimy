$(document).ready(function() {


    $('#credit-select .hidden-elements .line').click(function () {
      var text = $(this).text();
      $(this).parent().parent().find('.active-element').html(text);
      $(this).parent().find('.line').removeClass('active');
      $(this).addClass('active');
      var value = $(this).data('val');

      if ($(this).parent().parent().is('#credit-select')) {
        var arrayValue = [];

        if (value == "0") {
            arrayValue = [];
            arrayValue = [
                ['На карту', '/card', '/card'],
                ['На Киви-кошелек', '/qiwi', '/qiwi'],
                ['С 18 лет', '/zajm-18', '/zajm-18'],
                ['Со 100% одобрением', '/100-procentov', '/100-procentov'],
                ['С плохой кредитной историей', '/history', '/history'],
                ['Без отказа', '/bez-otkaza', '/bez-otkaza'],
                ['Без процентов', '/besplatnyj-zajm', '/besplatnyj-zajm'],
                ['Без карты', '/bez-karty', '/bez-karty'],
                ['Без звонков', '/bez-zvonkov', '/bez-zvonkov'],
                ['Долгосрочные', '/dolgosrochnye', '/dolgosrochnye'],
            ];
        }
        if (value == "1") {
            arrayValue = [];
            arrayValue = [
                ['Москва', '/zalogi/moskva', '/zalogi/moskva'],
                ['Санкт-Петербург', '/zalogi/sankt-peterburg', '/zalogi/sankt-peterburg'],
                ['Новосибирск', '/zalogi/novosibirsk', '/zalogi/novosibirsk'],
                ['Ростов-на-Дону', '/zalogi/rostov-na-donu', '/zalogi/rostov-na-donu'],
                ['В Краснодаре', '/zalogi/krasnodar', '/zalogi/krasnodar'],
                ['В Екатеринбурге', '/zalogi/ekaterinburg', '/zalogi/ekaterinburg'],
                ['В Самаре', '/zalogi/samara', '/zalogi/samara'],
                ['В Челябинске', '/zalogi/chelyabinsk', '/zalogi/chelyabinsk'],
                ['В Тюмени', '/zalogi/tyumen', '/zalogi/tyumen'],
                ['В Улан-Удэ', '/zalogi/ulan-udje', '/zalogi/ulan-udje'],
            ];
        }
        if (value == "2") {
            arrayValue = [];
            arrayValue = [
                ['Со 100% одобрением', '/online-credit/100-procentnoe-odobrenie', '/online-credit/100-procentnoe-odobrenie'],
                ['С моментальным решением', '/online-credit/momentalnoe-reshenie', '/online-credit/momentalnoe-reshenie'],
                ['С диф. платежами', '/online-credit/differencirovannye-platezhi', '/online-credit/differencirovannye-platezhi'],
                ['Без посещения банка', '/online-credit/bez-posesheniya-banka', '/online-credit/bez-posesheniya-banka'],
                ['Без подтверждения дохода', '/online-credit/bez-podtverzhdeniya-dohoda', '/online-credit/bez-podtverzhdeniya-dohoda'],
                ['Без отказа', '/online-credit/bez-otkaza', '/online-credit/bez-otkaza'],
                ['С 18 лет', '/online-credit/18-let', '/online-credit/18-let'],
                ['С 20 лет', '/online-credit/20-let', '/online-credit/20-let'],
                ['Сроком от 3 лет', '/online-credit/3-goda', '/online-credit/3-goda'],
                ['Сроком от 5 лет', '/online-credit/5-let', '/online-credit/5-let'],
            ];
        }
        if (value == "3") {
            arrayValue = [];
            arrayValue = [
                ['Со 100% одобрением', '/credit-cards/so-100-procentnym-odobreniem', '/credit-cards/so-100-procentnym-odobreniem'],
                ['С плохой кредитной историей', '/credit-cards/plohaja-kreditnaja-istorija', '/credit-cards/plohaja-kreditnaja-istorija'],
                ['Без проверок', '/credit-cards/bez-proverok', '/credit-cards/bez-proverok'],
                ['Без отказа', '/credit-cards/bez-otkaza', '/credit-cards/bez-otkaza'],
                ['По паспорту', '/credit-cards/pasport', '/credit-cards/pasport'],
                ['Для безработных', '/credit-cards/dlya-bezrabotnyh', '/credit-cards/dlya-bezrabotnyh'],
                ['С 18 лет', '/credit-cards/18-let', '/credit-cards/18-let'],
                ['С 21 года', '/credit-cards/21-god', '/credit-cards/21-god'],
                ['С 23 лет', '/credit-cards/23-goda', '/credit-cards/23-goda'],
                ['С доставкой курьером', '/credit-cards/kuryerom', '/credit-cards/kuryerom'],
            ];
        }
        if (value == "5") {
            arrayValue = [];
            arrayValue = [
                ['Для ИП', '/rko/dlya-ip', '/rko/dlya-ip'],
                ['За один день', '/rko/za-odin-den', '/rko/za-odin-den'],
                ['В евро', '/rko/v-evro', '/rko/v-evro'],
                ['Выгодные', '/rko/vygodnye', '/rko/vygodnye'],
                ['Дешевые', '/rko/deshevyj', '/rko/deshevyj'],
                ['С торговым эквайрингом', '/rko/torgovyi-ekvajring', '/rko/torgovyi-ekvajring'],
                ['С интернет-эквайрингом', '/rko/internet-ekvajring', '/rko/internet-ekvajring'],
                ['С зарплатным проектом', '/rko/zarplatnye-proekty', '/rko/zarplatnye-proekty'],
                ['С валютным контролем', '/rko/valyutnyj-kontrol', '/rko/valyutnyj-kontrol'],
                ['С оформлением онлайн', '/rko/onlajn', '/rko/onlajn'],
            ];
        }

        var textHtml = '';
        //console.log(arrayValue.length);

        for (var i = 0; i < arrayValue.length; i++) {

          if (i == 0) {
            textHtml += '<span class="line active" data-val="' + arrayValue[i][1] + '" data-url="' + arrayValue[i][2] + '">' + arrayValue[i][0] + '</span>';
            $('#credit-select2 .active-element').text(arrayValue[i][0]);
            $('#credit-select2 .active-element').attr('data-val',arrayValue[i][1]);
          } else {
            textHtml += '<span class="line" data-val="' + arrayValue[i][1] + '" data-url="' + arrayValue[i][2] + '">' + arrayValue[i][0] + '</span>';
          }
        }
        //console.log(textHtml);

        $('#credit-select2 .hidden-elements').html(textHtml);

        $('#credit-select2 .hidden-elements .line').click(function () {
          var text = $(this).text();
          $(this).parent().parent().find('.active-element').html(text);
          $(this).parent().find('.line').removeClass('active');
          $(this).addClass('active');
        });
      } // end if parent parent is credic select
    });



    $('.reviews').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });


    $('.experts').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });




   var ArrValues = [
            '- сервис подбора микрозаймов №1 в Рунете',
            '- быстрый и удобный сервис подбора кредитов и карт',
            '- сервис бесплатной проверки кредитного рейтинга',
            '- сервис поиска надежных залоговых компаний',
            '- сервис подбора банков для расчетно-кассового обслуживания',
            '- полезные финансовые статьи, инфографика и обучающие видео',
            '- самый полный каталог микрофинансовых компаний России',
            '- вы скорее всего попали сюда по рекомендации'
                ];
                var typed = new Typed('#typed', {
                strings: ArrValues,
                typeSpeed: 60,
                backSpeed: 0,
                smartBackspace: true, // this is a default
                loop: true
              });


$('.index-cards-count > div').click(function(){
    location.href = $(this).attr('data-url');
});

});











// поиск в сайдбаре для категории 1
$('#slf_zaimy').on('submit',function(){
    slf_zaimy();
    return false;
});

$('#find_by_options-1').on('click',function(){
    slf_zaimy();
});


function slf_zaimy(){
    $('#load_more').show();
    window.number_page = 1;
    var slf_summ = $('#slf_summ').val();
    var slf_time = $('#slf_time').val();
    var slf_percent = $('#slf_percent').val();
    var slf_age = $('#slf_age').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    var options = Array();

    $('.options-list span').each(function(){
        if($(this).find('input:checked')) options.push ($(this).find('input:checked').val());
    });

    window.sidebar_listings['slf_summ'] = slf_summ;
    window.sidebar_listings['slf_time'] = slf_time;
    window.sidebar_listings['slf_percent'] = slf_percent;
    window.sidebar_listings['slf_age'] = slf_age;

    $.ajax({
        type: "GET",
        url: "/actions/load_cards_for_listings",
        //url: "/actions/cards_sorting",
        data: {
            '_token': token,
            'field': window.field,
            'page': 1,
            'listing_id': window.listing_id,
            'count_on_page': window.count_on_page,
            'category_id': 1,
            'section_type': 2,
            'options': options,
            'sort_type': window.sort_type,
            'slf_summ': slf_summ,
            'slf_time': slf_time,
            'slf_percent': slf_percent,
            'slf_age': slf_age,

        },
        success: function(data){
            $('.offers-list').html(data['code']);
            update_img_and_bg();
            if(data['load']){
                $('#load_more').show();
            } else {
                $('#load_more').hide();
            }

        }
    });
}



$(function(){
    $.ajax({
        type: "GET",
        url: "/index-base-cards-load",
        //data: data,
        success: function(data){
            $('.offers-list').prepend(data['code']);
            update_img_and_bg();
            //$('#load_more_index_page').show();
        }
    });
});

window.number_page = 1;
window.listing_id = -1;
window.category_id = 1;
window.count_on_page = 10;
window.field = 'km5';
window.sort_type = 'desc';
window.sidebar_listings = {};
if(document.body.clientWidth > 768){
    //$('header').addClass('fixed');
    $(window).scroll(function() {
        if($(this).scrollTop() != 0) {
            $('header').addClass('fixed');
            $('header').css('border-bottom','1px solid #ddd');
        } else {
            $('header').css('border-bottom','0');
            $('header').removeClass('fixed');
        }
    });
}


$(function(){


$('#debitCardsSlider').slick({
    dots: false,
    infinite: false,
    speed: 300,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

$('#creditCardsSlider').slick({
    dots: false,
    infinite: false,
    speed: 300,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

$('#creditsSlider').slick({
    dots: false,
    infinite: false,
    speed: 300,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

    $('#autoCreditsSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

$('#rkoSlider').slick({
    dots: false,
    infinite: false,
    speed: 300,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});


    $('#newsSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });


    $('#zalogiSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });


    $('#cashBackSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('#insuranceSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

});
