$(function(){
    var boolParamAv = $('#search_bar_form_parametreAvances');
    var boolCarteActive = $('#search_bar_form_ActiverCarte');
    var formParamAv = $('#paramAv');
    var formCarteActive = $('#mapSearch');


    //Activation désactivation des champs de recherche avancée******************
    hideShow(boolCarteActive.is(':checked'), formCarteActive);
    hideShow(boolParamAv.is(':checked'), formParamAv);

    boolParamAv.on("click", function(){

        hideShow($(this).is(':checked'), formParamAv);
        hideShow(boolCarteActive.is(':checked'), formCarteActive);

        if(!$(this).is(':checked'))
            $('#search_bar_form_searchBar').attr('required',true);
        else
            $('#search_bar_form_searchBar').removeAttr('required');
    });
    boolCarteActive.on("click", function(){

        hideShow($(this).is(':checked'), formCarteActive);

    });

    //Récupération des données du slider.
    var showValSlider = $('#distanceFromPoint');
    var slider = $('#search_bar_form_distanceDuCentre');
    var sliderDOM = document.getElementById("search_bar_form_distanceDuCentre");

    //Réécrire l'événement attaché au slider si la carte est activée, pour ajouter le changement de superficie.
    showValSlider.val(slider.val());
    sliderDOM.oninput = function() {
        showValSlider.val(this.value);
    }


});


//Cacher/montrer le reste du formulaire en fonction de l'état vrai ou faux de la case cochée
//bool check, form DOM.  return
function hideShow(check, form)
{

    if(check)
    {
        $(form).slideDown();


    }
    else
    {
        $(form).slideUp();
    }
    toggleMendatory(check,form);
}



//Les champs du formulaire deviennent obligatoires ou non.
//bool check, form DOM.  return
function toggleMendatory(check, form)
{
    if(check)
    {
        $(form).find('.form-control').attr('required', true);
        $(form).find('.required').removeClass('required');
    }
    else
    {
        $(form).find('.form-control').removeAttr('required');
        $(form).find('.required').removeClass('required');
    }
}


//Quand la carte de recherche est prête: on y met un marqueur, on dessine un cercle autour et on s'assure d'avoir les bons événements.
function OnMapRReady(mapR)//map = élément du dom
{

    $(function(){
        var latIDR= "#search_bar_form_latitude";
        var longIDR= "#search_bar_form_longitude";

        //init********************************************
        var markerR = new google.maps.Marker({
            position: mapR.getCenter(),
            map: mapR,
            title: "Positionnez-moi là où vous avez vu l'oiseau"

        });



        markerR.setDraggable(true); //Déplacable
        $(latIDR).val(mapR.getCenter().lat());
        $(longIDR).val(mapR.getCenter().lng());

        var circle = new google.maps.Circle();

        moveCircle(circle,mapR, new google.maps.LatLng($(latIDR).val(), $(longIDR).val()));

        //Events ******************************************

        $('#search_bar_form_parametreAvances').click(function(){
            $('#paramAv').removeClass('collapse');
            google.maps.event.trigger(mapR, 'resize');

        });
        $('#search_bar_form_ActiverCarte').click(function(){

            google.maps.event.trigger(mapR, 'resize');

        });
        markerR.addListener('drag', function(){
            $(latIDR).val(markerR.getPosition().lat());
            $(longIDR).val(markerR.getPosition().lng());
            //RE dessiner le cercle.
            moveCircle(circle,mapR,new google.maps.LatLng($(latIDR).val(), $(longIDR).val()));
        });


        //re-focus on position
        $('#useMyPosR').on('click',function(event){
            if(navigator.geolocation) //Si pas internet explorer, lol
            {
                navigator.geolocation.getCurrentPosition(function (position) {
                    markerR.setPosition({lat: position.coords.latitude, lng: position.coords.longitude});
                    $(latIDR).val(markerR.getPosition().lat());
                    $(longIDR).val(markerR.getPosition().lng());
                    mapR.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
                    //Redessiner le cercle
                    moveCircle(circle,mapR,new google.maps.LatLng($(latIDR).val(), $(longIDR).val()));
                });

            }
            else
            {
                //$(this).append() // Activez votre géolocalisation
            }

        });

        //Réécrire l'événement attaché au slider si la carte est activée, pour ajouter le changement de superficie du cercle.
        var showValSlider = document.getElementById('distanceFromPoint');
        var sliderDOM = document.getElementById("search_bar_form_distanceDuCentre");

        sliderDOM.oninput = function() {
            showValSlider.value = this.value;
            moveCircle(circle,mapR,new google.maps.LatLng($(latIDR).val(), $(longIDR).val()));
        }


    });
}

function moveCircle(circle,map,center)
{
    var radius = document.getElementById('distanceFromPoint').value;

    circle.setOptions({
        strokeColor: '#009900',
        strokeOpacity: 0.7,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.20,
        radius: parseFloat(radius),
        center: center,
        map: map,
        bounds: map.getBounds()
    });


}