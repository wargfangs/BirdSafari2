var map;
var searchMap;

function initMap()
{
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 47.5, lng: 2.2},
        zoom: 5,
        streetViewControl: false
    });


    OnMapReady(map); // Toutes les pages du bundle observation doivent implémenter cette fonction. Toutes ont besoin de la carte.


    searchMap = new google.maps.Map(document.getElementById('mapR'), {
        center: {lat: 47.5, lng: 2.2},
        zoom: 4,
        streetViewControl: false
    });

    if(navigator.geolocation) //Si pas internet explorer, lol
    {
        navigator.geolocation.getCurrentPosition(function(position){
            searchMap.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
            searchMap.setZoom(10);
        });

    }
    OnMapRReady(searchMap);





}
