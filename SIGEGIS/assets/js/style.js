
$("input[id*='valider'],input[id*='validerLocalite'], #connection_form :submit, .boutonAdmin").button();

$("#radio,#radio2").buttonset();

$("#ouvrir, #comparer, .boutonAdmin").css("width","200px");
$(".boutonAdmin").css("width","80%");

$('#menu li a').hover(
function() {     
$(this).animate({'backgroundColor':'rgba(0,0,0,0.5)'},'fast');               
},   
function() {   
	$(this).animate({'backgroundColor' :'rgba(0,0,0,0.2)'},'fast');
}
)
.mousedown(function() {   
	$(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.1)'}, 'fast');   
})
.mouseup(function() {     
    $(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.5)'}, 'fast');   
    });   
  
	$("#zone_des_filtres").addClass("ui-state-default ui-corner-all");
$(".zone_des_options").addClass("ui-state-default ui-corner-all");

$("#accordion").accordion({ header: "h3" });
$("#accordion2").accordion({ header: "h3" });

//Boutons accueil
$(".boutons").button({
    icons: {
        secondary: "ui-icon-gear"            
    },
    text: true
});