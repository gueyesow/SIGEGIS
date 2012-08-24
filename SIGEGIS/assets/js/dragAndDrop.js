
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

$("#MoveRightCandidat,#MoveLeftCandidat").on("click",function(event) {
	var id = $(event.target).attr("id");
	var selectFrom = id == "MoveRightCandidat" ? "#choixCandidatA" : "#choixCandidatB";
	var moveTo = id == "MoveRightCandidat" ? "#choixCandidatB" : "#choixCandidatA";
	var selectedItems = $(selectFrom + " :selected").toArray();
	$(moveTo).append(selectedItems);
	$(moveTo+ " :selected").removeAttr("selected");
	selectedItems.remove;
});

$(".move").button();

$("#valider").on("click",function(event) {
	listeAnnees="";
	listeCandidats="";
	paramBis=$sources.val()+","+$("#ana_tour").val()+","+$("#localite").val();
	
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

$("#choixmultipleB,#choixCandidatB").children().each(function(){$(this).removeAttr("selected");});
