$(document).ready(function() {
	$("#bloc_visualiser,#bloc_analyser").hide();
	
	$(".exemple").button();
	$(".exemple").css("width","200px");
	
	$("#menu ul li:not(':first,:gt(8)')").hide();
	$("#types_affichage input").attr("disabled","disabled");
	if(!$.getUrlVar("type")) $("#zone_des_filtres select").attr("disabled","disabled");
	
	$(".exemple").on("click",function(){
		$("#bloc_visualiser,#bloc_analyser").hide();
		$("#bloc_"+$(this).attr("id")).show("animated");
	});
});