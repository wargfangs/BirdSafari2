$(
    if (HelpCenter.user.role=="NATURALIST"){
     $("div.institution").show();
    }

    if (HelpCenter.user.role=="USER"){
     $("div.confirmStatus").show();		
    }
);