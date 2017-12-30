$(function(){
    $('#myCarousel-3').carousel({interval: 15000});

    var drh1 = $("#text1").text();
    var drh2 = $("#text2").text();
    var drh3 = $("#text3").text();

    var MVP1 = "Parole de Georges\n" +
    "Wow, wow… Merci beaucoup à tous… D’habitude je suis assez à l’aise pour parler, mais aujourd’hui je suis un peu stressé.\n" +
    "Tout d’abord, j’aimerais remercier Le Président pour avoir changé ma vie, j’ai vraiment compris en quoi voler consistait.\n" +
    "Ce n’est qu’une manière d’inspirer les gens. Et j’ai aujourd’hui \n";

    var MVP2= "compris tout ça. Depuis 10 ans que j’accompagne Le Président, je lui montre les plus beaux lieux et mes plus beaux amis. Mais c’est vrai que c’est compliqué. Quand il devrait faire froid il fait chaud, les aires de repos n’ont plus d’eau, quand il devrait faire chaud il fait froid.\n" +
    "C’est pour ca que j’aide l’association.\n" +
    "On souffre tous les jours" ;

    var MVP3= "de ces changements \n" +
    "et je sais que "+ "“Nos amis les oiseaux” aide les oiseaux comme\n" +
    "moi à vivre mieux et à protéger mes proches.\n" +"CV :\n" +
    "4 chutes de nid\n" +
    "2 550 moustiques abattus (compte en cours)\n" +
    "300 000 Km parcourus\n" +
    "45 pays visités\n" +
    "modèle photo pour plus de 150 000 personnes\n" +
    "Mais des difficultés toujours plus grandes pour voyager.\n";


    var president1 = "Je n’ai pas appris à parler aux Oiseaux, malgré toute une vie à les observer, " +
        "je continue d’essayer de les comprendre.Le rêve ultime n’est-il pas de voler? Ils vivent notre rêve si simplement " +
        "et nous offrent la chance de pouvoir observer ce ballet volant. \n" + "Je ne sais pas si" +
        " c’est par jalousie ou par mépris. Mais l’animal, qui vit le plus grand rêve de l'humanité souffre";

    var president2 = "tous les jours un peu plus de l'activité de cet homme au rêve si grand et à la compassion si petite.\n" +
        "\n" +
        "C’est Georges, notre[mascotte - un silvius malinus (geai moqueur)- qui m’a poussé à créer " +
        "Nos amis les oiseaux : il était en plein hiver coincé en France car il avaitloupé son vol pour" +
        " un pays plus chaud. J’ai compris que je ne pourrais plus observer ce spectacle volant bien longtemps" ;

    var president3 = " si personne ne s'intéressait aux conséquences des activités de l’homme sur la nature.\n" +
        "\n" +
        "Et nous sommes nombreux à faire ce constat aujourd’hui et nous nous soutenons.\n" + "<strong> le temps de prendre une photo des oiseaux que nous croisons</strong>,nous prenons part au projet de la WWF,le CNRS, les Nations Unies pour aider la nature à reprendre ses droits.\n" +
        "\n" +
        "Vous aussi participez !";


    $('#myCarousel-3').on('slide.bs.carousel', function(){

        $("#text1").stop().fadeOut(200);
        $("#text2").stop().fadeOut(300);
        $("#text3").stop().fadeOut(400);
    });

    $('#myCarousel-3').on('slid.bs.carousel', function(){
        var id = $('#myCarousel-3').find('div[class*="active"]').attr('id');

        //console.log(id);
        if(id == "pres")
        {
            $("#text1").html(president1);
            $("#text2").html(president2);
            $("#text3").html(president3);
        }
        else if(id == "dir")
        {
            $("#text1").html(drh1);
            $("#text2").html(drh2);
            $("#text3").html(drh3);
        }
        else if(id == "mvp")
        {
            $("#text1").html(MVP1);
            $("#text2").html(MVP2);
            $("#text3").html(MVP3);
        }

        $("#text1").stop().show();
        $("#text2").stop().fadeIn();
        $("#text3").stop().fadeIn();
    });

});