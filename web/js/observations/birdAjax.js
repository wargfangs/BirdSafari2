$(function(){


    var path = Routing.generate('API_birdname',null,true);
   $.getJSON(path,function(data){
       //Boucle sur les données récupérées:
       $.each(data, function(key, bird)
       {
           var text= bird.nomVern;

           //console.log(val.nomComplet);
           if($.trim(text) != "")
           {
               text = text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
               $(".birdnames").append("<option value=" + text + ">" + text + "</option>");
           }


       });
       //
   });
});