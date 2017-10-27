var geocoder; // Geocoding: process by with we extrapolate addresses from coordinate.

$(function(){


});


function initialize()
{
    geocoder = new google.maps.Geocoder();
    //Récupération du point de la carte.

    //Création de la carte avec comme point de référence le point au dessus.


}

function geocodeNow(map)
{
    var address = document.getElementById('observation_form_place').value;
    geocoder.geocode( {"address": address}, function(results,status){
        if (status == 'OK') {
            map.setCenter(results[0].geometry.location);
            marker.setOption({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });

}