//Les données sont envoyées dans la vue dans une balise marquée par "coords". et séparée par un ';'
//On ajoute les interactions entre carte et liste des observations ici.

function OnMapReady(map)
{

    $(function(){
        var lats = [];
        var lngs = [];
       $('.coords').each(function(ind)
       {

          var data = $(this).text().split(";");

          positionnerSurCarte(map,data[0],data[1], $(this), data[2]);
          lats.push(data[0]);
          lngs.push(data[1]);
       });
       if(lats.length != 0)
       {
           repositionnerCarte(map,lats,lngs);
       }
        /* Events *********************/
        $('.storing').click(function(){
            google.maps.event.trigger(map, 'resize');

        });

    });

    /* Events ***************************** */
    //Most of them are in positionnerSurCarte(...) below
}

//Positionner les éléments suivant leur latitude et leur longitude.
//Nécessite une carte
function positionnerSurCarte(map,latitude,longitude, coordElt, id)
{
    var lat = new google.maps.LatLng(latitude,longitude);


    var marker = new google.maps.Marker({
        position: lat,
        map: map,
        title: "Une obs"
    });
    marker.setMap(map);


    var tabLigne = $('tr[id$="line-'+id+'"]');

    var contentString = "<h4>Observation n°"+ id+"</h4>";
    var infoWin = new google.maps.InfoWindow({content:contentString});
    if( tabLigne!= null) //if we are in a tab + map page
    {
        //OnHover marker, highlight line of tab + load content.
        $(tabLigne).on('mouseenter',function(e){

            $(this).css('color','#889547');
            infoWin.open(map,marker);

            $(this).on('mouseleave',function(e){ // Put closing event
                $(this).css('color','#000000');
                infoWin.close();
            });
        });
        $(tabLigne).on('click',function(e){ // On click: info window stays

            $(this).css('color','#889547');
            infoWin.open(map,marker);
            $(this).off('mouseleave');
        });
        $(tabLigne).on('mouseleave',function(e){

            $(this).css('color','#000000');
            infoWin.close();
        });


        marker.addListener('mouseover',function(e){

            $(tabLigne).css('color','#889547');
            infoWin.open(map,marker);

            this.addListener('mouseout',function(e){ // Put closing event
                $(tabLigne).css('color','#000000');
                infoWin.close();
            });
        });
        marker.addListener('mouseout',function(e){

            $(tabLigne).css('color','#000000');
            infoWin.close();
        });
        marker.addListener('click',function(e){ // On click: info window stays

            $(tabLigne).css('color','#889547');
            infoWin.open(map,marker);
            google.maps.event.clearListeners(marker, 'mouseout');
        });


    }




    //OnHover line of tab, show content above + highlight line

    //On
}

function repositionnerCarte(map, lats,lngs)
{
    var latmin=lats[0];
    var latmax=lats[0];
    var lngmin =lngs[0];
    var lngmax=lngs[0];
    lats.forEach(function(el){
        latmin = Math.min(el,latmin);
        latmax = Math.max(el,latmax);
    });

    lngs.forEach(function(el2){
        lngmin = Math.min(el2,lngmin);
        lngmax = Math.max(el2,lngmax);
    });

    //console.log(latmin+" "+ latmax +" "+ lngmin +" "+ lngmax);
    map.setCenter(new google.maps.LatLng((latmin+latmax)/2, (lngmin+lngmax)/2)); // Centre la carte sur le milieu des points

    //Condition de zoom. Si ecart = X, définir zoom max.
    var ecart = Math.max(latmax-latmin, lngmax-lngmin);
    console.log(ecart);
    if(ecart < 0.03)                        //Dé-zoom suffisament
        map.setZoom(13);
    else if(ecart >= 0.03 && ecart < 0.6)
        map.setZoom(10);
    else if(ecart >= 0.6 && ecart < 1)
        map.setZoom(9);
    else if(ecart< 1.6 && ecart >= 1)
        map.setZoom(8);
    else if(ecart >= 1.6 && ecart < 2 )
        map.setZoom(7);
    else if(ecart >= 2 && ecart < 4 )
        map.setZoom(6);
    else if(ecart >= 4 && ecart < 10 )
        map.setZoom(5);
    else
        map.setZoom(5);
}

function connectLineAndMarker(lines, marker)
{
    //
}