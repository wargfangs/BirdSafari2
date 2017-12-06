$( function(){
    var regSub = $('#registrationSubmit');
    hideShow2($('#fos_user_registration_form_acceptCgu').is(':checked'), regSub);


    $('#fos_user_registration_form_acceptCgu').on("click", function(){
        //console.log($('#nat').is(':checked'));
        hideShow2($(this).is(':checked'), regSub);

    });

});

//Cacher/montrer le reste du formulaire en fonction de l'état vrai ou faux de la case cochée
//bool check, form DOM.  return
function hideShow2(check, form)
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
    }
    else
    {
        $(form).find('.form-control').removeAttr('required');
    }
}

