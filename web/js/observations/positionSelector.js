//Ce script doit être placé en dessous de la carte pour fonctionner


function OnMapReady(map)//map = élément du dom
{

    $(function(){
        var latID= "#observation_form_latitude";
        var longID= "#observation_form_longitude";

        var marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            title: "Positionnez-moi là où vous avez vu l'oiseau"

        });
        //marker.setIcon()
        marker.setDraggable(true);

        $(latID).val(map.getCenter().lat());
        $(longID).val(map.getCenter().lng());

        marker.addListener('drag', function(){
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());
        });

        $('#useMyPos').on('click',function(event){
            if(navigator.geolocation) //Si pas internet explorer, lol
            {
                navigator.geolocation.getCurrentPosition(function (position) {
                    marker.setPosition({lat: position.coords.latitude, lng: position.coords.longitude});
                    $(latID).val(marker.getPosition().lat());
                    $(longID).val(marker.getPosition().lng());
                });

            }
            else
            {
                //$(this).append() // Activez votre géolocalisation
            }

        });
    });
}