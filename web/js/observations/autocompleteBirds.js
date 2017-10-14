$(function(){

    autocompletBirdNames($('#observation_form_birdname'));
});

function autocompletBirdNames(jqueryObj)
{
    jqueryObj.autocomplete({
    disable:false,
    source: function(req,resp){
        $.ajax({
            url : 'http://localhost/BirdSafari/web/app_dev.php/Obs/API/birds',
            dataType : 'json',
            data : {
                name_startWith: $('#observation_form_birdname_lbNom').val(),
                maxRows: 5
            },
            success : function(donnee){
                resp($.map(donnee, function(bird){
                    if(bird.nomVern != null)
                        if(bird.nomVern.startsWith( $('#observation_form_birdname_lbNom').val().charAt(0).toUpperCase() + $('#observation_form_birdname_lbNom').val().slice(1).toLowerCase()))
                            return bird.nomVern;
                        else if(bird.lbNom.startsWith($('#observation_form_birdname_lbNom').val().charAt(0).toUpperCase() + $('#observation_form_birdname_lbNom').val().slice(1).toLowerCase()))
                            return bird.lbNom;
                }));
            },
            error: function(req,str,error)
            {
                console.log(str);
            }

        })

    },
    minLength : 3
});
}