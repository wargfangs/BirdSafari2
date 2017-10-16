//Les données sont envoyées dans la vue dans une balise marquée par "coords". et séparée par un ';'
//On ajoute les interactions entre carte et liste des observations ici.

function OnMapReady(map)
{
    $(function(){
       $('.coords').each(function(ind){
           var latLn = $(this).text().split(";");
          positionnerSurCarte(map,latLn[0],latLn[1]);
       });
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