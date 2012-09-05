/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE | DESS 2ITIC
 * Description: Gestion des filtres de la partie analyse 
 */
	
	$("#chartdiv1").hide();
	/**
	 * Ajouter en paramètre la source,le tour à représenter
	 */
	function putGrid(url){
	if ($("#list").text()!="") $("#list").setGridParam({url: url,page:1}).trigger("reloadGrid");
	else{ 
	$("#list").jqGrid({		
		autowidth:true,
		url: url,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Nom du candidat','Lieu de vote','Année','Nombre de voix'],
	    colModel :[ 
	      {name:'nomCandidat', index:'nomCandidat',search:true},
	      {name:'lieuDeVote', index:'lieuDeVote', width:80},
	      {name:'annee', index:'annee', width:80},
	      {name:'nbVoix', index:'nbVoix', width:80}  
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,200],
	    sortname: 'nbVoix',
	    sortorder: 'desc',
	    viewrecords: true,
	    gridview: true,
	}).navGrid("#pager",{edit:false,add:false,del:false,search:false});
	$(".ui-jqgrid-bdiv").removeAttr("style");
	}
	}

function refreshBarChart(json){
		
		var i=0;
		
		var series=JSON.parse(json);			
		chart1.setTitle({text: series[0].titre},{text: series[0].sous_titre});
		chart1.xAxis[0].setCategories(series[0].categories);
		if ( chart1.series.length > 0 ) {
			a_supprimer=chart1.series.length;
			for(i=0;i<a_supprimer;i++) {				
				chart1.series[0].remove();							
			}
		}
						
		for(i=0;i<series[1].length;i++){				
			chart1.addSeries(series[1][i],false);
		}
		
		chart1.redraw();
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
	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val();

	if($.getUrlVar("type")==="presidentielle") paramBis+=","+$("#ana_tour").val();
	paramBis+=","+$("#localite").val();
	
	$("#theGrid,#chartdiv1").show();
	$("#help").hide();
	
	//$("#list").setGridWidth(906);
	
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
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserAnnee',    
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),	     
		success: function(json) {
			refreshBarChart(json);
		}    
	});
	putGrid("http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserAnnee?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"));
	//$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserAnnee?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");	
});

//-------------------------------------------------//
//Bouton VALIDER de l'analyse suivant une localité  //
//-------------------------------------------------//
$("#validerLocalite").on("click",function(event) {

	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	
	listeLocalites="";
	listeCandidats="";
	
	paramBis=$sources.val();	
	
	if($.getUrlVar("type")==="presidentielle") paramBis+=","+$tours.val();
	
	paramBis+=","+$elections.val()+","+$.getUrlVar("type");
		
	$("#theGrid,#chartdiv1").show();
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
	
	if ($("select[name*=ana_localite2]").val()==="pays") { paramBis+="&niveau=pays"; }
	if ($("select[name*=ana_localite2]").val()==="region") { paramBis+="&niveau=reg";}
	if ($("select[name*=ana_localite2]").val()==="departement") {paramBis+="&niveau=dep";}
	if ($("select[name*=ana_localite2]").val()==="centre") { paramBis+="&niveau=cen";}
		
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarAnalyserLocalite',    
		data:'param='+paramBis+"&typeElection="+$.getUrlVar("type"),	     
		success: function(json) {
			refreshBarChart(json);										
		}    
	});
	
	putGrid("http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyserLocalite?niveau=dep&param="+paramBis+"&typeElection="+$.getUrlVar("type"));	
});

// Efface les champs de sélection multiple 
$("#choixmultipleB,#choixCandidatB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").children().each(function(){$(this).removeAttr("selected");});

$("#accordion_item2 select[id*='ana']").on("change",function(){
	$("#choixMultipleLocalitesB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
	$pays.change();
	
});