/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE | DESS 2ITIC
 * Description: Gestion des filtres de la partie analyse 
 */

// Rappel: chart1 <=> Bar et chart2 <=> Line | "save" à true indique que l'on compare deux élections

/*
 * Crée le grid si ce n'est pas déjà fait et y charge des données qui sont reçues à partir du lien "url"
 */
function putGrid(url){	
		
	if(save) {balise="#list2";pager="#pager2";}
	else {balise="#list";pager="#pager";}
		
	if ($(balise).text()!="") $(balise).setGridParam({url: url,page:1}).trigger("reloadGrid");
	else{
		$(balise).jqGrid({		
			autowidth:true,
			url: url,
		    datatype: 'xml',
		    mtype: 'GET',
		    colNames:['Nom du candidat','Lieu de vote','Année','Nombre de voix'],
		    colModel :[ 
		      {name:'nomCandidat', index:'nomCandidat'},
		      {name:'lieuDeVote', index:'lieuDeVote', width:80},
		      {name:'annee', index:'annee', width:80},
		      {name:'nbVoix', index:'nbVoix', width:80, formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}}  
		    ],
		    pager: pager,
		    rowNum:20,
		    rowList:[20,30,50,100,200],	    
		    viewrecords: true,
		    gridview: true,
		}).navGrid(pager,{edit:false,add:false,del:false,search:false});
				
		if(!$("#grid")[0].checked || $("#grid")[0].disabled) {$("#theGrid").hide("animated");if(save) $("#theGrid2").hide("animated");}
		
		$(".ui-jqgrid-bdiv").removeAttr("style"); // Width: 100%
	}	
}

/*
 * Recharge le graphique représenté par l'objet theChart avec les données reçues via l'objet JSON "json"
 * theChart Object Objet représentant le graphique
 * json JSON Object Objet JSON
 */
function refreshChart(theChart,json){
	
	var i=0;
	
	var series=JSON.parse(json);			
	
	if (save) $("#titleGrid2").text(series[0].titre); else $("#titleGrid1").text(series[0].titre);
	theChart.setTitle({text: series[0].titre},{text: series[0].sous_titre});
	theChart.xAxis[0].setCategories(series[0].categories);
	if ( theChart.series.length > 0 ) {
		a_supprimer=theChart.series.length;
		for(i=0;i<a_supprimer;i++) {				
			theChart.series[0].remove();							
		}
	}
					
	for(i=0;i<series[1].length;i++){				
		theChart.addSeries(series[1][i],false);
	}
	
	theChart.redraw();
}


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
	lastPressedButton="valider";
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#dialog_zone_des_options").dialog('close');
	
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val();

	if(typeElection=="presidentielle") paramBis+=","+$("#ana_tour").val();
	paramBis+=","+$("#localite").val();
	
	if ($("#bar")[0].checked) $("#chartdiv1").show();
	$("#help").hide();
	
	$("#choixmultipleB").children().each(function() {
		if(listeAnnees=="") listeAnnees+=$(this).text();
		else listeAnnees+=","+$(this).text();
	});
	
	$("#choixCandidatB").children().each(function() {
		if(listeCandidats=="") listeCandidats+=$(this).val();
		else listeCandidats+=","+$(this).val();
	});
	
	paramBis+="&listeAnnees="+listeAnnees+"&listeCandidats="+listeCandidats;
	
	if ($("select[name=niveauAgregation1]").val()=="pays") { paramBis+="&niveau=pays"; }
	if ($("select[name=niveauAgregation1]").val()=="region") { paramBis+="&niveau=reg";}
	if ($("select[name=niveauAgregation1]").val()=="departement") {paramBis+="&niveau=dep";}
	if ($("select[name=niveauAgregation1]").val()=="centre") { paramBis+="&niveau=cen";}
	
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserAnnee',    
		data:'param='+paramBis+"&typeElection="+typeElection,	     
		success: function(json) {
			refreshChart(chart1,json);
			if($("#line")[0].checked) refreshChart(chart2,json);
		}    
	});
	
	putGrid("http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserAnnee?niveau=dep&param="+paramBis+"&typeElection="+typeElection);
	
});

//-------------------------------------------------//
//Bouton VALIDER de l'analyse suivant une localité  //
//-------------------------------------------------//
$("#validerLocalite").on("click",function(event) {
	lastPressedButton="validerLocalite";
	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#dialog_zone_des_options").dialog('close');
	
	listeLocalites="";
	listeCandidats="";
	
	paramBis=$sources.val();	
	
	paramBis+=","+$elections.val();
	
	if(typeElection=="presidentielle") paramBis+=","+$tours.val();	
		
	if ($("#bar")[0].checked) $("#chartdiv1").show();
	$("#help").hide();
	
	$("#choixMultipleLocalitesB").children().each(function() {
		if(listeLocalites=="") listeLocalites+=$(this).text();
		else listeLocalites+=","+$(this).text();
	});
	
	$("#choixCandidatLocaliteB").children().each(function() {
		if(listeCandidats=="") listeCandidats+=$(this).val();
		else listeCandidats+=","+$(this).val();
	});
	
	paramBis+="&listeLocalites="+listeLocalites+"&listeCandidats="+listeCandidats;
	
	if ($("select[name=niveauAgregation2]").val()=="pays") { paramBis+="&niveau=pays"; }
	if ($("select[name=niveauAgregation2]").val()=="region") { paramBis+="&niveau=reg";}
	if ($("select[name=niveauAgregation2]").val()=="departement") {paramBis+="&niveau=dep";}
	if ($("select[name=niveauAgregation2]").val()=="centre") { paramBis+="&niveau=cen";}
		
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserLocalite',    
		data:'param='+paramBis+"&typeElection="+typeElection,	     
		success: function(json) {
			refreshChart(chart1,json);	
			if($("#line")[0].checked) refreshChart(chart2,json);
		}    
	});
	
	putGrid("http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserLocalite?param="+paramBis+"&typeElection="+typeElection);	
});


// Efface les champs de sélection multiple 
$("#choixmultipleB,#choixCandidatB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").children().each(function(){$(this).removeAttr("selected");});

$("#accordion_item2 select[id*='ana']").on("change",function(){
	$("#choixMultipleLocalitesB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
	$pays.change();
});

$("#dialog_zone_des_options").dialog({
	autoOpen: false,
	width: 800,
	buttons: {
		"Fermer": function() {
			$(this).dialog("close");
			$("#ouvrir").show();
		}
	},
	closeOnEscape: true ,
	resizable: false,
	beforeClose: function(event, ui) { $("#ouvrir").show(); }
});


$("#ouvrir").on("click",function(){
	$("#dialog_zone_des_options").dialog('open');
	$("#ouvrir").hide();
	$(".zone_des_options *:not([#bar,#pie,#map)").removeAttr("disabled");
	$("#comparer").removeAttr("disabled");
	typeElection=$(".zone_des_options input:checked:not(#locale)").attr("id");
});

$("#comparer").on("click",function(){
	save=true;
	if(save) {balise="chartdiv2";if($("#line")[0].checked) baliseLine="chartdiv4";}
	else {balise="chartdiv1";if($("#line")[0].checked) baliseLine="chartdiv3";}
	
	$("#chartdiv2").show();
	if ($("#line")[0].checked) $("#chartdiv4").show();
	
	$("#dialog_zone_des_options").dialog('open');
	
	putBar(balise);
	putLine(baliseLine);
});

