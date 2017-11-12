$( function(){
    var natForm = $('#champsNat');
    hideShow($('#fos_user_registration_form_confirmationStatus').is(':checked'), natForm);


    $('#fos_user_registration_form_confirmationStatus').on("click", function(){
        //console.log($('#nat').is(':checked'));
        hideShow($(this).is(':checked'), natForm);

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
    }
    else
    {
        $(form).find('.form-control').removeAttr('required');
    }
}

