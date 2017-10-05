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



});