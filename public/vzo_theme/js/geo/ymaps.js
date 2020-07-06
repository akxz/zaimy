/*
document.body.onload = function() {
clientID = yaCounter38176370.getClientID();
window.clientID = clientID;
console.log('METKIKA: '+ clientID);

region = ymaps.geolocation.region;
country = ymaps.geolocation.country;
window.city = ymaps.geolocation.city;
console.log('city: '+ window.city);
}
*/

$(document).ready(function() {

    //clientID = yaCounter38176370.getClientID();
    //window.clientID = clientID;
    //console.log('METKIKA: '+ clientID);

    if (YMaps.location) // Проверяем, доступна ли геопозиция
    {
        //console.log("Longitude: " + YMaps.location.longitude); // Выведем долготу
        //console.log("Latitude: " + YMaps.location.latitude);   // Выведем широту
        //$(".country").val(YMaps.location.country); // Достанем в input страну
        window.city = YMaps.location.city;   // Достанем в input регион (область)
        console.log('city: '+ window.city);
		$(".geo_cities_current").text(window.city);
		$(".geo_cities_wrap").removeClass("hide");
    }
    else
        console.log("Пожалуйста, разрешите доступ к использованию Вашей геопозиции!");
});