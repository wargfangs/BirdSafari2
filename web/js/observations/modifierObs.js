$(function(){
    var check =$("#keepPic");
    var pic=$("#oldPic");
    var selector=$("#observation_form_image_file");
    if(document.getElementById('oldPic') != null)
    {
        hideShowPic(check, pic,selector);
        check.on('click', function(){
            hideShowPic($(this).is(':checked'),pic,selector);
        });
    }



});


function hideShowPic(check, pic, sel) // bool, picElt, choiceFileElt
{

    if(!check)
    {
        $(sel).slideDown();
        $(pic).slideUp();


    }
    else
    {
        $(sel).slideUp();
        $(pic).slideDown();
    }
}