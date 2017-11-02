//Ce script doit être placé en dessous de la carte pour fonctionner


function OnMapReady(map)//map = élément du dom
{

    $(function(){
        var latID= "#observation_form_latitude";
        var longID= "#observation_form_longitude";
        var placeBtn = document.getElementById('observation_form_place');
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

        //map.controls[google.maps.ControlPosition.TOP_LEFT].push(placeBtn);

        //Autocomplete address
        var autocompletePlace = new google.maps.places.Autocomplete(placeBtn);
        autocompletePlace.bindTo('bounds',map);
        var infos = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder;

        //Lat and long fields pre-filled
        $(latID).val(map.getCenter().lat());
        $(longID).val(map.getCenter().lng());

        //Events **************************************************
        marker.addListener('drag', function(){ //Fill the lat lng fields with marker position.
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());
        });

        $('form[name="observation_form"]').on('submit', function(e) {
            e.preventDefault();
            reverseGeo(latID,longID,marker,geocoder,map, infos);

            this.submit();
        });
        //reverse geocoding for address.  Fill place field with town and administrative region + fill latlng.
        marker.addListener('dragend', function(){
            $(latID).val(marker.getPosition().lat());
            $(longID).val(marker.getPosition().lng());

            reverseGeo(latID,longID,marker,geocoder,map, infos);
        });

        //autocomplete place, for the user to select address among specified others.
        autocompletePlace.addListener('place_changed', function(){
            infos.close();
            marker.setVisible(false);
            var place = autocompletePlace.getPlace(); // Contains all data from
            if(!place.geometry){
                marker.setVisible(true);
                window.alert('Pas de détail disponible pour '+ place.name +' ');
            }

            if(place.geometry.viewport) // relocate map
            {
                map.fitBounds(place.geometry.viewport); //Zoom a la distance idéale pour voir l'objet
            }
            else
            {
                map.setCenter(place.geometry.location);
                map.setZoom(15);
            }

            marker.setIcon(({ // Change form of marker
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location); //Position of marker
            marker.setVisible(true);

            //Sending to form lat lng.
            $(latID).val(place.geometry.location.lat());
            $(longID).val(place.geometry.location.lng());

            //Send text propositions
            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
            infos.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infos.open(map,marker);

        });


        //On valid, use reverse coding to fill place field.



        //re-focus on position on click
        $('#useMyPos').on('click',function(event){
            if(navigator.geolocation) //Si pas internet explorer, lol
            {
                navigator.geolocation.getCurrentPosition(function (position) {
                    marker.setPosition({lat: position.coords.latitude, lng: position.coords.longitude});
                    $(latID).val(marker.getPosition().lat());
                    $(longID).val(marker.getPosition().lng());
                    map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
                    reverseGeo(latID,longID,marker,geocoder,map, infos);
                });
            }
            else
            {
                //$(this).append() // Activez votre géolocalisation
            }

        });



        });
}

//Function that
function reverseGeo(lat,lng,marker, geocoder, map, infowindow) {
    var latlng = {lat: parseFloat($(lat).val()), lng: parseFloat($(lng).val())};
    infowindow.close();
    geocoder.geocode({'location': latlng}, function(results, status) {
        if (status === 'OK') {
            if (results[1]) {
                var address = "";
                results[1].address_components.forEach(function(placeComp){
                    //console.log(placeComp);
                    if(placeComp.types.includes("locality") || placeComp.types.includes("country"))
                    {
                        address += placeComp.long_name + " ";
                    }
                });

                $('#observation_form_place').val(address);


                marker.setOptions({
                    position: latlng,
                    map: map
                });
                infowindow.setContent(results[1].formatted_address);
                infowindow.open(map, marker);
            } else {

                //write : Unknown address.
                $('#observation_form_place').val("Adresse inconnue");
            }
        } else {
            //Write: unknown address
            $('#observation_form_place').val("Adresse inconnue");
        }
    });
}