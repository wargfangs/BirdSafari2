$(function(){
    var times = 0;
    $('html').on('mouseleave', function(){
        $('#videoPop').modal("show");
        times++;
        if(times>2) // 3 times in out of html.
            $('html').off('mouseleave');
    });


});