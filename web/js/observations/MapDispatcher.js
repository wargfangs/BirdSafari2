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

          positionnerSurCarte(map,data[0],data[1]);
          lats.push(data[0]);
          lngs.push(data[1]);
       });
       if(lats.length != 0)
       {
           repositionnerCarte(map,lats,lngs);
       }

    });
}

//Positionner les éléments suivant leur latitude et leur longitude.
//Nécessite une carte
function positionnerSurCarte(map,latitude,longitude)
{
    var lat = new google.maps.LatLng(latitude,longitude);


    var marker = new google.maps.Marker({
        position: lat,
        map: map,
        title: "Une obs"
    });
    marker.setMap(map);
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
    map.setCenter(new google.maps.LatLng((latmin+latmax)/2, (lngmin+lngmax)/2));

    //Condition de zoom. Si ecart = X, définir zoom max.
    var ecart = Math.max(latmax-latmin, lngmax-lngmin);
    console.log(ecart);
    if(ecart < 0.03)
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