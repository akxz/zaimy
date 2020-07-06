/*
$('.geo_cities_check').on('click', function(){
    document.cookie = "GEO_CITY=" + $(this).attr('data-value') + "; path=/;";
    $(this).hide();
    $('.geo_cities_close').hide();
    var pageUrl = document.URL;
    var cityUrl = $(this).attr('data-url');

    if (cityUrl === undefined) {
        return false;
    }

    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: "/actions/online-proverka-na-koronavirus",
        data: {
            '_token': token,
            'action': 'before'
        }
    });

    var re = /\/$/;
    pageUrl = pageUrl.replace(re,'');
    location.href = pageUrl + '/' + cityUrl;

});
$('.change-geo-city').on('click',function () {
    $('#citiesModal').addClass('in');
    $.ajax({
        type: "GET",
        url: "/actions/zaimy/geo/get_all_cities",
        success: function(data){
            console.log(data);
            $('.geo_cities_list').html('');
            // data = JSON.parse(data);
            var str ='';
            data.forEach(function(items) {
                str += '<div class="geo_cities_row">';
                $.each( items, function(key, value) {
                    if(key != ''){
                        str += '<div class="geo_cities_block">';
                        str += `<h5>${key}</h5>`;
                        $.each( value, function(item,city) {
                            str += `<a class="form-line" data-city="${city[2]}" href="${city[0]}">${city[1]}</a>`;
                        });
                        str += '</div>';
                    }
                });
                str += '</div><br>';
            });
            $('.geo_cities_list').append(str);
        }
    });
});
$("#geo_search_city").keyup(function(){
    _this = this;
    $.each($(".geo_cities_list a"), function() {
        if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1){
            $(this).hide();
        } else{
            $(this).show();
        }
    });
    $('.geo_cities_block').each(function(){
       var allCount = $(this).find('a').length;
       var hiddenCount = $(this).find('a').filter(function() { return $(this).css("display") == "none" }).length;
       console.log(hiddenCount, hiddenCount);
       if (hiddenCount == allCount) {
           $(this).find('h5').hide();
       } else {
           $(this).find('h5').show();
       }
    });
});
$(document).on('click','.geo_cities_block a',function(e){
    e.preventDefault();
    document.cookie = "GEO_CITY=" + $(this).attr('data-city') + "; path=/;";
    location.href = $(this).attr('href');
});
*/
