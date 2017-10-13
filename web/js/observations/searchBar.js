$(function(){
    var boolParamAv = $('#search_bar_form_parametreAvances');
    var boolCarteActive = $('#search_bar_form_ActiverCarte');
    var formParamAv = $('#paramAv');
    var formCarteActive = $('#mapSearch');

    hideShow(boolParamAv.is(':checked'), formParamAv);
    hideShow(boolCarteActive.is(':checked'), formCarteActive);

    boolParamAv.on("click", function(){
        hideShow($(this).is(':checked'), formParamAv);

    });
    boolCarteActive.on("click", function(){

        hideShow($(this).is(':checked'), formCarteActive);

    });


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