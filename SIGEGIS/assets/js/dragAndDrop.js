/*	function getGrid(){
		param=$sources.val()+","+$("#ana_tour").val();
		if ( $.getUrlVar("niveau") ) {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau='+$.getUrlVar("niveau")+'&param='+param;}
		else {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?param='+param;}
	
		$("#help").after('<div id="theGrid"><table id="list"></table><div id="pager"></div></div>');
		$("#help").remove();
		$("#list").jqGrid({		
			url:$url,
			autowidth:true,
		    datatype: 'xml',
		    mtype: 'GET',
		    colNames:['Nom du candidat','Année','Nombre de voix'],
		    colModel :[ 
		      {name:'nomCandidat', index:'nomCandidat', search:true},
		      {name:'annee', index:'annee', width:80,sortable:true},
		      {name:'nbVoix', index:'nbVoix', width:80, align:'nbVoix',sortable:true}  
		    ],
		    pager: '#pager',
		    rowNum:20,
		    rowList:[20,30,50,100],
		    sortname: 'nbVoix',
		    sortorder: 'desc',
		    viewrecords: true,
		    gridview: true,
		}).navGrid("#pager",{edit:true,add:true,del:true}).trigger("reloadGrid");
	}
*/	
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

$("#MoveRightCandidat,#MoveLeftCandidat").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightCandidat" ? "#choixCandidatA" : "#choixCandidatB";
	var moveTo = id == "MoveRightCandidat" ? "#choixCandidatB" : "#choixCandidatA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
});
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

$("#valider").on("click",function(event) {
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val()+","+$("#ana_tour").val()+","+$("#localite").val();
	
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
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyse',    
		data:'param='+paramBis,	     
		success: function(json) {
			$("#chartdiv1").append(json);										
		}    
	});
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyse',    
		data:'param='+paramBis,	     
		success: function(json) {
			$("#chartdiv2").append(json);										
		}    
	});
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyse?niveau=dep&param="+paramBis,page:1}).trigger("reloadGrid");
	
});

$("#validerLocalite").on("click",function(event) {
	$("#grid,#bar,#map").removeAttr("disabled");
	$("#dialog_zone_des_options").dialog('close');
	listeLocalites="";
	listeCandidats="";
	paramBis=$sources.val()+","+$tours.val()+","+$elections.val();
	
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
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyseLocalite',    
		data:'param='+paramBis,	     
		success: function(json) {
			$("#chartdiv1").append(json);										
		}    
	});
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyseLocalite',    
		data:'param='+paramBis,	     
		success: function(json) {
			$("#chartdiv2").append(json);										
		}    
	});
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridAnalyseLocalite?niveau=dep&param="+paramBis,page:1}).trigger("reloadGrid");
	
});

$("#choixmultipleB,#choixCandidatB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").children().each(function(){$(this).removeAttr("selected");});
