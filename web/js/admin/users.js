$( function(){
    var names = $('.collapse');
    names.on("hide.bs.collapse", function(){
        $('[href$='+$(this).attr('id')+']').find('span').toggleClass('glyphicon-chevron-right');//Change chevron
        $('[href$='+$(this).attr('id')+']').find('span').toggleClass('glyphicon-chevron-down');
    });
    names.on("show.bs.collapse", function(){


        $('[href$='+$(this).attr('id')+']').find('span').toggleClass('glyphicon-chevron-right');//Change chevron
        $('[href$='+$(this).attr('id')+']').find('span').toggleClass('glyphicon-chevron-down');
    });
    console.log(names);
});
