
$("input[id*='valider'],input[id*='validerLocalite'], #connection_form :submit, .boutonAdmin, .boutonsimple").button();

$("#radio,#radio2").buttonset();

$(".boutonAdmin").css({"width":"80%","margin":"5px"});

$("#accordion").accordion({ header: "h3" });

$("#accordion2").accordion({ header: "h3" });

//Boutons accueil
$(".boutons").button({
    icons: {
        secondary: "ui-icon-gear"            
    },
    text: true
});
$(".boutonhelp").button({
    icons: {
        secondary: "ui-icon-help"            
    },
    text: true
});