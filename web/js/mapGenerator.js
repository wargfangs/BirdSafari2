var map;

function initMap()
{
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 47.5, lng: 2.2},
        zoom: 5
    });

    if(navigator.geolocation) //Si pas internet explorer, lol
    {
        navigator.geolocation.getCurrentPosition(function(position){
            map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
            map.setZoom(10);
        });

    }



    OnMapReady(map); // Toutes les pages du bundle observation doivent implémenter cette fonction. Toutes ont besoin de la carte.
}
