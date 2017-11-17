//Une variable var page doit être définie dans un script en amont de celui ci. Sinon, impossible de déplacer les éléments du menu.

$(function(){
    if(page == "all")
        $('#allObs').toggleClass("active");
    if(page == "add")
        $('#addObs').toggleClass("active");
    if(page == "my")
        $('#myObs').toggleClass("active");
    if(page == "wait")
        $('#waitingObs').toggleClass("active");

    console.log("Helo");
    //Ajouter des événements d'ouverture et de fermeture au parent des boutons ayant la classe storing.
    $('.storing').click(function () {
        $(this).find('span').toggleClass('glyphicon-chevron-right');
        $(this).find('span').toggleClass('glyphicon-chevron-down');
        $(this).next('div').slideToggle('slow',smartClose($(this)));

    });



});

    //button : jqueryObj bouton surlequel on a appuyé.
    function smartClose(button)
    {
        //console.log("Contient c6 "+ button.parent().hasClass("col-lg-6"));
        if(button.attr("id") === 'obsLayout')
        {
            //écouter les événements de redimensionnement de la carte pour retirer le bug d'affichage.


            $('#mapLayout').parent().toggleClass("col-lg-6");
            $('#mapLayout').parent().toggleClass("col-lg-11");

        }
        else if(button.attr("id") === 'mapLayout')
        {

            $('#obsLayout').parent().toggleClass("col-lg-6");
            $('#obsLayout').parent().toggleClass("col-lg-11");
        }

    }

   /* function OnMapReady(map)
    {
        console.log("Called");

    }*/