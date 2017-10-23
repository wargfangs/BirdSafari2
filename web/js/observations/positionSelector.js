//Ce script doit être placé en dessous de la carte pour fonctionner


function OnMapReady(map)//map = élément du dom
{

    $(function(){
        var latID= "#observation_form_latitude";
        var longID= "#observation_form_longitude";
        var autocompleAddress = "#autocompleteAddress"

        //Add marker
        var marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            title: "Positionnez-moi là où vous avez vu l'oiseau"

        });
        marker.setDraggable(true);
        //marker.setIcon()      //Change marker later

        //Lat and long fields pre-filled
        $(latID).val(map.getCenter().lat());
        $(longID).val(map.getCenter().lng());

        //Events **************************************************
        marker.addListener('drag', function(){
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());
        });


        //re-focus to position
        $('#useMyPos').on('click',function(event){
            if(navigator.geolocation) //Si pas internet explorer, lol
            {
                navigator.geolocation.getCurrentPosition(function (position) {
                    marker.setPosition({lat: position.coords.latitude, lng: position.coords.longitude});
                    $(latID).val(marker.getPosition().lat());
                    $(longID).val(marker.getPosition().lng());
                    map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
                });

            }
            else
            {
                //$(this).append() // Activez votre géolocalisation
            }

        });
    });
}