//Ce script doit être placé en dessous de la carte pour fonctionner


function OnMapReady(map)//map = élément du dom
{

    $(function(){
        var latID= "#observation_form_latitude";
        var longID= "#observation_form_longitude";
        var autocompleAddress = "#autocompleteAddress"
        if(navigator.geolocation) //Si pas internet explorer, lol
        {
            navigator.geolocation.getCurrentPosition(function(position){
                map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
                map.setZoom(9);
            });

        }
        //Add marker
        var marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            title: "Positionnez-moi là où vous avez vu l'oiseau"

        });
        marker.setDraggable(true);
        //marker.setIcon()      //Change marker later


        //Si on a déjà des données d'observation (cas de la page de modification)
        if($(latID).val()!=0 && $(longID).val()!=0) {
            marker.setPosition(new google.maps.LatLng($(latID).val(),$(longID).val()));
            map.setCenter(marker.getPosition());
        }
        //Lat and long fields pre-filled
        $(latID).val(map.getCenter().lat());
        $(longID).val(map.getCenter().lng());

        //Events **************************************************
        marker.addListener('drag', function(){
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());
        });
        marker.addListener('dragend', function(){
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());
            var req = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + $(latID).val()+ "," + $(longID).val() +"&key=AIzaSyC7OxaSU742N4m30QGtTnkwb7TSoBSCzrc";
            console.log(req);

            $.ajax(req,function(data){
               console.log(data);
            });

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