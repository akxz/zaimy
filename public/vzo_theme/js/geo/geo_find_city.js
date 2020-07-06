/*
$(document).ready(function() {
    if (window.load_city_from_yandex) {
        $.ajax({
            type: "GET",
            url: "/actions/zaimy/geo/find_city_by_name?name="+window.city,
            success: function(data){
                if (data.url !== undefined) {
                    $('.geo_cities_current').text(window.city);
                    $('.geo_cities_check').attr('data-url', data.url);
                    $('.geo_cities_check').attr('data-name',data.imenitelny);
                    $('.geo_cities_check').attr('data-value',data.url);
                } else {
                    $('.geo_cities_current').text('Москва');
                    $('.geo_cities_check').attr('data-url','moskva');
                    $('.geo_cities_check').attr('data-name','Москва');
                    $('.geo_cities_check').attr('data-value','moskva');
                }
            }
        });

    }
    $('.geo_cities_wrap').removeClass('hide');
});
*/