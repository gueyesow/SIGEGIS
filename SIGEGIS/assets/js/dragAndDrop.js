/*
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE | DESS 2ITIC
 * Description: Gestion des filtres et des interactions pour l'utilitaire de comparaison
 * Cette partie comporte 2 fonction:
 * 1. putGrid(url) qui gere les grid
 * 2. refreshChart(theChart,json) qui gere les diagrammes en colonnes 
 */

// Rappel: chart1 <=> Bar et chart2 <=> Line | "save" à true indique que l'on compare deux élections

// LES BOUTONS DE LA PARTIE ANALYSER LES RESULTATS SONT DANS CE FICHIER 
// TODO Réaménager le code si nécessaire

var baliseBar; // L'ID du conteneur pour le diagramme en colonnes 
var baliseLine; // L'ID du conteneur pour le diagramme circulaire 

//Crée le grid si ce n'est pas déjà fait et y charge des données qui sont reçues à partir du lien "url"

function putGrid(url){	
	// Rappel: Afficher le conteneur pour que le diagramme puisse s'afficher correctement
	if (request1OrRequest2=="comparer") {baliseGrid="#list2";pager="#pager2";$("#theGrid2").show();}
	else {baliseGrid="#list";pager="#pager";$("#theGrid1").show();}
	
		
	if ($(baliseGrid).text()!="") $(baliseGrid).setGridParam({url: url,page:1}).trigger("reloadGrid");
	else{
		$(baliseGrid).jqGrid({		
			autowidth:true,
			url: url,
		    datatype: 'xml',
		    mtype: 'GET',
		    colNames:['Nom du candidat','Lieu de vote','Année','Nombre de voix'],
		    colModel :[ 
		      {name:'nomCandidat', index:'nomCandidat'},
		      {name:'lieuDeVote', index:'lieuDeVote'},
		      {name:'annee', index:'annee'},
		      {name:'nbVoix', index:'nbVoix', formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}}  
		    ],
		    pager: pager,
		    rowNum:20,
		    rowList:[20,30,50,100,200],	    
		    viewrecords: true,
		    gridview: true,
		}).navGrid(pager,{edit:false,add:false,del:false,search:false});
				
		if(!$("#grid")[0].checked || $("#grid")[0].disabled) {$("#theGrid1,#theGrid2").hide("animated");} 
	}	
	$(".ui-jqgrid-bdiv").removeAttr("style");
	
	// Si l'option grid n'est pas choisie, cacher les grids
	if (!$("#grid")[0].checked) $("#theGrid1,#theGrid2").hide();
}

/*
 * Recharge le graphique représenté par l'objet theChart avec les données reçues via l'objet JSON "json"
 * theChart Object Objet représentant le graphique
 * json JSON Object Objet JSON
 */
function refreshChart(theChart,json){
	
	var i=0;

	if(json.length)
	{
		
		var series=JSON.parse(json);
		
		if(series[1].length)
		{				
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
				$(".ui-state-error").remove();
				theChart.addSeries(series[1][i],false);
			}
			
			theChart.redraw();
			theChart.hideLoading();
		}
		else {
			$(".ui-state-error").remove();
			$("#"+baliseBar).before('<div class="ui-state-error"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> Une erreur s\'est produite durant l\'éxécution de la requête. Veuillez vérifier les paramètres choisis.<br></p></div><br />');
			return;
		}
	}
	else
	{
		$(".ui-state-error").remove();
		$("#"+baliseBar).before('<div class="ui-state-error"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> Une erreur s\'est produite durant l\'éxécution de la requête. Veuillez vérifier les paramètres choisis.<br></p></div><br />');
		return;
	}
}


// Boutons du SwapList des années pour l'analyse suivant une année 	
$("#MoveRight,#MoveLeft").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRight" ? "#choixmultipleA" : "#choixmultipleB";
	var moveTo = id == "MoveRight" ? "#choixmultipleB" : "#choixmultipleA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	var $granuliteDepExiste=false;

	// S'il existe des donnees de granularite departement selectionnees alors on supprime le filtre "Centre".
	$.each(selectedItems,function(index,value) 
	{
		if($GRANULARITE[$(this).text()]!="centre" && moveTo=="#choixmultipleB"){
			$granuliteDepExiste=true;
			$("#niveauAgregation1 > option:last").hide();
			$("#niveauAgregation1").val("departement");
			if ($("#niveauAgregation1").val()=="pays") { methode="getPays";parametres_analyse+="&niveau=pays&anneeDecoupage="+$("#decoupage_annee").val();}
			else if ($("#niveauAgregation1").val()=="region") { methode="getRegions";parametres_analyse+="&niveau=reg";}
			else if ($("#niveauAgregation1").val()=="departement") { methode="getDepartements";parametres_analyse+="&niveau=dep";}
			
			$url=base_url+"filtres/"+methode+"?typeElection="+typeElection+"&anneeDecoupage="+$("#decoupage_annee").val();

			$.ajax({        							
				url: $url,    
				dataType: 'json', 
				success: function(json) {			
					$("#localite").empty();			
					$.each(json, function(index, value) {         
						$("#localite").append('<option value="'+ index +'">'+ value +'</option>');     
					});		
					$("#choixmultipleB").change();
				}    
			});			
			//$(moveTo).append(selectedItems);
		}
		else {$("#niveauAgregation1 > option:last").show();}
	});
	
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
	
	if(!$granuliteDepExiste) {$("#choixmultipleB").change();$granuliteDepExiste=false;}
});

//Boutons du SwapList des localités pour l'analyse suivant une localité
$("#MoveRightLocalite,#MoveLeftLocalite").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightLocalite" ? "#choixMultipleLocalitesA" : "#choixMultipleLocalitesB";
	var moveTo = id == "MoveRightLocalite" ? "#choixMultipleLocalitesB" : "#choixMultipleLocalitesA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	
	// S'il existe des donnees de granularite departement selectionnees alors on supprime le filtre "Centre".
	/*$.each(selectedItems,function(index,value) 
	{
		if($GRANULARITE[$elections.val()]!="centre" && moveTo=="#choixMultipleLocalitesB"){
			$("#niveauAgregation2 > option:last").hide();
			$("#niveauAgregation2").val("departement");
			$elections.change();
			/*if ($("#niveauAgregation2").val()=="pays") { methode="getPays";parametres_analyse+="&niveau=pays&anneeDecoupage="+$("#decoupage_annee").val();}
			else if ($("#niveauAgregation2").val()=="region") { methode="getRegions";parametres_analyse+="&niveau=reg";}
			else if ($("#niveauAgregation2").val()=="departement") { methode="getDepartements";parametres_analyse+="&niveau=dep";}
			
			$url=base_url+"filtres/"+methode+"?typeElection="+typeElection+"&anneeDecoupage="+$("#decoupage_annee").val();

			$.ajax({        							
				url: $url,    
				dataType: 'json', 
				success: function(json) {			
					$("#localite").empty();			
					$.each(json, function(index, value) {         
						$("#localite").append('<option value="'+ index +'">'+ value +'</option>');     
					});		
					$("#choixmultipleB").change();
				}    
			});			
			$(moveTo).append(selectedItems);
		}
		else {$("#niveauAgregation1 > option:last").show();$("#choixmultipleB").change();}
	});*/
	
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

// ----------------------------------------------- //
// Bouton VALIDER de l'analyse suivant les années  //
// ----------------------------------------------- //
$("#validerAnnees").on("click",function(event) {
	lastPressedButton="validerAnnees";
	
	$("#dialog_zone_des_options").dialog('close');
	
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val();

	if(typeElection=="presidentielle") paramBis+=","+$("#ana_tour").val();
	paramBis+=","+$("#localite").val();
	
	if ($("#bar")[0].checked) {$("#chartdiv1").show();if (save) $("#chartdiv2").show();}
	if ($("#grid")[0].checked) {$("#theGrid1").show();if (save) $("#theGrid2").show();}
	if ($("#line")[0].checked) {$("#chartdiv3").show();if (save) $("#chartdiv4").show();}
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
	
	putBar(baliseBar);
	putLine(baliseLine);
	chart1.showLoading('<div style="margin:auto;margin-top:150px;">En cours de chargement...<br/><img src="../../assets/images/ajax-loader.gif" width="128px" /></div>');
	chart2.showLoading('<div style="margin:auto;margin-top:150px;">En cours de chargement...<br/><img src="../../assets/images/ajax-loader.gif" width="128px" /></div>');
	
	$.ajax({        							
		url: base_url+'analyser/getBarAnalyserSuivantAnnee',    
		data:'param='+paramBis+"&typeElection="+typeElection,	     
		success: function(json) {
			refreshChart(chart1,json);
			//if($("#line")[0].checked) refreshChart(chart2,json);
			refreshChart(chart2,json);
		}    
	});
	
	putGrid(base_url+"analyser/getGridAnalyserSuivantAnnee?niveau=dep&param="+paramBis+"&typeElection="+typeElection);
	
});

// ------------------------------------------------ //
// Bouton VALIDER de l'analyse suivant les localités //
// ------------------------------------------------ //
$("#validerLocalites").on("click",function(event) {
	lastPressedButton="validerLocalites";
	
	$("#dialog_zone_des_options").dialog('close');
	
	listeLocalites="";
	listeCandidats="";
	
	paramBis=$sources.val();	
	
	paramBis+=","+$elections.val();
	
	if(typeElection=="presidentielle") paramBis+=","+$tours.val();	

	if ($("#bar")[0].checked) {$("#chartdiv1").show();if (save) $("#chartdiv2").show();}
	if ($("#grid")[0].checked) {$("#theGrid1").show();if (save) $("#theGrid2").show();}
	if ($("#line")[0].checked) {$("#chartdiv3").show();if (save) $("#chartdiv4").show();}
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
		
	putBar(baliseBar);
	putLine(baliseLine);
	
	$.ajax({        							
		url: base_url+'analyser/getBarAnalyserSuivantLocalite',    
		data:'param='+paramBis+"&typeElection="+typeElection,	     
		success: function(json) {
			refreshChart(chart1,json); 
			refreshChart(chart2,json);
		}    
	});
	
	putGrid(base_url+"analyser/getGridAnalyserSuivantLocalite?param="+paramBis+"&typeElection="+typeElection);
});


// Efface les champs de sélection multiple 
$("#choixmultipleB,#choixCandidatB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").children().each(function(){$(this).removeAttr("selected");});

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

// Ouvrir le popup de l'utilitaire de comparaison (Par defaut, il s'agit d'une simple interface permettant de faire des requetes) 
$("#ouvrir").on("click",function(){
	request1OrRequest2=$(this).attr("id");
	$("#dialog_zone_des_options").dialog('open');
	$("#ouvrir").hide();
	$(".zone_des_options *:not(#pie,#map)").removeAttr("disabled");
	$("#comparer").removeAttr("disabled");
	typeElection=$(".zone_des_options input:checked:not(#locale)").attr("id");
	baliseBar="chartdiv1";
	baliseLine="chartdiv3";
});

// Quitter le mode comparaison
$("#simple").on("click",function(){
	save=false;
	$("#theGrid2,#chartdiv2,#chartdiv4,#simple").hide("animated");
});

// Reprendre tout a zero
$("#reset").on("click",function(){
	window.location.reload();
});

// Activer le mode comparaison 
$("#comparer").on("click",function(){
	save=true;
	request1OrRequest2=$(this).attr("id");
	
	$("#chartdiv2").show(); 
	$("#chartdiv4").show();
	
	$("#dialog_zone_des_options").dialog('open');
	baliseBar="chartdiv2";
	baliseLine="chartdiv4";
	
	$("#simple").show("animated");
});
