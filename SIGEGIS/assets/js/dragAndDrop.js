/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE | DESS 2ITIC
 * Description: Gestion des filtres de la partie analyse 
 */


// Boutons du SwapList des années pour l'analyse suivant une année 	
$("#MoveRight,#MoveLeft").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRight" ? "#choixmultipleA" : "#choixmultipleB";
	var moveTo = id == "MoveRight" ? "#choixmultipleB" : "#choixmultipleA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
	$("#choixmultipleB").change();	
});

//Boutons du SwapList des localités pour l'analyse suivant une localité
$("#MoveRightLocalite,#MoveLeftLocalite").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightLocalite" ? "#choixMultipleLocalitesA" : "#choixMultipleLocalitesB";
	var moveTo = id == "MoveRightLocalite" ? "#choixMultipleLocalitesB" : "#choixMultipleLocalitesA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
	$("#choixMultipleLocalitesB").change();	
});

//Boutons du SwapList des candidats pour l'analyse suivant une année 	
$("#MoveRightCandidat,#MoveLeftCandidat").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightCandidat" ? "#choixCandidatA" : "#choixCandidatB";
	var moveTo = id == "MoveRightCandidat" ? "#choixCandidatB" : "#choixCandidatA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
});

// Boutons du SwapList des candidats pour l'analyse suivant une localité 	
$("#MoveRightCandidatLocalite,#MoveLeftCandidatLocalite").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightCandidatLocalite" ? "#choixCandidatLocaliteA" : "#choixCandidatLocaliteB";
	var moveTo = id == "MoveRightCandidatLocalite" ? "#choixCandidatLocaliteB" : "#choixCandidatLocaliteA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
});

$(".move").button();

//-------------------------------------------------//
//	Bouton VALIDER de l'analyse suivant une année  //
//-------------------------------------------------//
$("#valider").on("click",function(event) {
<<<<<<< HEAD
	
=======
>>>>>>> refs/heads/AmadeusBranch
	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val();
<<<<<<< HEAD
	
=======
>>>>>>> refs/heads/AmadeusBranch
	if($.getUrlVar("type")==="presidentielle") paramBis+=","+$("#ana_tour").val();
	paramBis+=","+$("#localite").val();
	
	$("#theGrid,#chartdiv1").show();
	$("#help").hide();
	$("#list").setGridWidth(906);
	
	$("#choixmultipleB").children().each(function() {
		if(listeAnnees=="") listeAnnees+=$(this).text();
		else listeAnnees+=","+$(this).text();
	});
	
	$("#choixCandidatB").children().each(function() {
		if(listeCandidats=="") listeCandidats+=$(this).val();
		else listeCandidats+=","+$(this).val();
	});
	
	paramBis+="&listeAnnees="+listeAnnees+"&listeCandidats="+listeCandidats;
	
	if ($("select[name*=ana_localite]").val()==="pays") { paramBis+="&niveau=pays"; }
	if ($("select[name*=ana_localite]").val()==="region") { paramBis+="&niveau=reg";}
	if ($("select[name*=ana_localite]").val()==="departement") {paramBis+="&niveau=dep";}
	if ($("select[name*=ana_localite]").val()==="centre") { paramBis+="&niveau=cen";}
	
	$.ajax({        							
<<<<<<< HEAD
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserAnnee',    
=======
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyse',    
>>>>>>> refs/heads/AmadeusBranch
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),	     
		success: function(json) {
			$("#chartdiv1").append(json);										
		}    
	});
	
	$.ajax({        							
<<<<<<< HEAD
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyserAnnee',    
=======
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyse',    
>>>>>>> refs/heads/AmadeusBranch
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),	     
		success: function(json) {
			$("#chartdiv2").append(json);										
		}    
	});
<<<<<<< HEAD
	
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserAnnee?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
=======
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyse?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
>>>>>>> refs/heads/AmadeusBranch
	
});

//-------------------------------------------------//
//Bouton VALIDER de l'analyse suivant une localité  //
//-------------------------------------------------//
$("#validerLocalite").on("click",function(event) {
<<<<<<< HEAD
	
=======
>>>>>>> refs/heads/AmadeusBranch
	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	
	listeLocalites="";
	listeCandidats="";
<<<<<<< HEAD
=======
	
	if($.getUrlVar("type")==="presidentielle") paramBis+=","+$tours.val();
	paramBis+=","+$("#localite").val();
>>>>>>> refs/heads/AmadeusBranch
	
	paramBis=$sources.val();

	if($.getUrlVar("type")==="presidentielle") paramBis+=","+$tours.val();
	
	paramBis+=","+$elections.val()+","+$.getUrlVar("type");
		
	$("#theGrid,#chartdiv1").show();
	$("#help").hide();
	$("#list").setGridWidth(906);
	
	$("#choixMultipleLocalitesB").children().each(function() {
		if(listeLocalites=="") listeLocalites+=$(this).text();
		else listeLocalites+=","+$(this).text();
	});
	
	$("#choixCandidatLocaliteB").children().each(function() {
		if(listeCandidats=="") listeCandidats+=$(this).val();
		else listeCandidats+=","+$(this).val();
	});
	
	paramBis+="&listeLocalites="+listeLocalites+"&listeCandidats="+listeCandidats;
	
	if ($("select[name*=ana_localite2]").val()==="pays") { paramBis+="&niveau=pays"; }
	if ($("select[name*=ana_localite2]").val()==="region") { paramBis+="&niveau=reg";}
	if ($("select[name*=ana_localite2]").val()==="departement") {paramBis+="&niveau=dep";}
	if ($("select[name*=ana_localite2]").val()==="centre") { paramBis+="&niveau=cen";}
	
	$.ajax({        							
<<<<<<< HEAD
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserLocalite',    
=======
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyseLocalite',    
>>>>>>> refs/heads/AmadeusBranch
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),	     
		success: function(json) {
			$("#chartdiv1").append(json);										
		}    
	});
	
	$.ajax({        							
<<<<<<< HEAD
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyserLocalite',    
=======
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyseLocalite',    
>>>>>>> refs/heads/AmadeusBranch
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),     
		success: function(json) {
			$("#chartdiv2").append(json);										
		}    
	});
<<<<<<< HEAD
	
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserLocalite?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
=======
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyseLocalite?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
>>>>>>> refs/heads/AmadeusBranch
	
});

// Efface les champs de sélection multiple 
$("#choixmultipleB,#choixCandidatB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").children().each(function(){$(this).removeAttr("selected");});
