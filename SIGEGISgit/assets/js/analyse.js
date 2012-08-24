
$(document).ready(function() {   	 

if($.getUrlVar("map")==="no") {$("#gbox_list").hide("animated");} else {$("#gbox_list").show("animated");}
if($.getUrlVar("bar")==="no") {$("#chartdiv1").hide("animated");$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")==="yes") {$("#chartdiv1").show("animated");$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")==="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")==="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");}
if($.getUrlVar("grid")==="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")==="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}
/**
 * CHOIX DU MODE DE REPRESENTATION DES DONNEES
 */
$("#types_affichage input").on( "change",function() {
	i=0;var idmode;

	$("#types_affichage input").each(function(){
		idmode=""+$(this).attr("id");
		valeur=($(this).attr("checked")==="checked")?"yes":"no";		
		mode+="&"+idmode+"="+valeur;			
	});
	
	
	if($.getUrlVar("niveau")) mode+="&niveau="+$.getUrlVar("niveau");
	
	
	
	//window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type="+$.getUrlVar("type")+mode;
	$.ajax({        							
		url: 'http://www.sigegis.ugb-edu.com/main_controller/analyser',    
		data:"type="+$.getUrlVar("type")+mode, 					     
		success: function(json) {
			if( $("#types_affichage input:eq(1)").attr("checked")!="checked" ) $("#chartdiv1").hide();	else $("#chartdiv1").show();
			if( $("#types_affichage input:eq(2)").attr("checked")!="checked" ) $("#chartdiv2").hide();	else $("#chartdiv2").show();
			if( $("#types_affichage input:eq(3)").attr("checked")!="checked" ) $("#theGrid").hide();	else $("#theGrid").show();
		}    
	});
});

/**
 * REDEFINITION DES VALEURS DES BOUTONS RADIOS (SELECTIONNER L'OPTION D'AFFICHAGE CHOISIE)
 */
$.each(types_election,function(){   
	if ($.getUrlVar("type")===""+this){
		$("#"+this).attr("checked","checked");
		if(""+this==="locale"||""+this==="municipale"||""+this==="regionale"||""+this==="rurale") {
			$("#types_elections").append(
				"<fieldset><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio' checked='checked' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio' /><label for='rurale'>Rurales</label></fieldset>");
			$("#locale").attr("checked","checked");
		}
	}
});
/**
 * RECHARGE DE LA PAGE A CHAQUE CHANGEMENT DU TYPE D'ELECTION A REPRESENTER
 */
$("#types_elections input").on( "change",function() {
	 var idelection=""+$(this).attr("id");
	$.each(types_election,function(){		
		if (idelection===""+this ){
			$("#types_affichage input").each(function(){
				idmode=""+$(this).attr("id");
				valeur=($(this).attr("checked")==="checked")?"yes":"no";		
				mode+="&"+idmode+"="+valeur;			
			});
			if($.getUrlVar("niveau")) mode+="&niveau="+$.getUrlVar("niveau");
			if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type="+this+mode;
			else window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type="+this;
		}
	});
});
		
	/**
	 * CACHER LES SELECT INUTILES
	 */
	if ($.getUrlVar("niveau")==="cen")
	{

	}
	else
	if ($.getUrlVar("niveau")==="col")
	{
		$("#filtrecentres").remove();
	}
	else
	if ($.getUrlVar("niveau")==="dep")
	{
		$("#filtrecollectivites").remove();
		$("#filtrecentres").remove();
	}
	else
	if ($.getUrlVar("niveau")==="reg")
	{
		$("#filtredepartements").remove();
		$("#filtrecollectivites").remove();
		$("#filtrecentres").remove();
	}
	else
	{
		$("#filtreregions").remove();	$("#filtredepartements").remove();	$("#filtrecollectivites").remove();	$("#filtrecentres").remove();
	}
	
	/**
	 * Ajouter en paramètre la source,le tour à représenter
	 */
	param=$sources.val()+","+$("#ana_tour").val();
	if ( $.getUrlVar("niveau") ) {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau='+$.getUrlVar("niveau")+'&param='+param;}
	else {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?param='+param;}

	
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
	}).navGrid("#pager",{edit:true,add:true,del:true});

		if ($.getUrlVar("niveau")==="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function()
			{
				param="";i=0;
				param+=$sources.val()+","+$("#ana_tour").val()+","+$centres.val();
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau=cen&param="+param,page:1}).trigger("reloadGrid");
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyse',    
					data:'niveau=cen&param='+param,        					     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyse',    
					data:'niveau=cen&param='+param,      					     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});			
			});			
		}
		else
		if ($.getUrlVar("niveau")==="dep")	// NIVEAU DEPARTEMENT 
		{		
			$departements.on("change",function()
			{
				param="";i=0;
				param+=$sources.val()+","+$("#ana_tour").val()+","+$departements.val();
						
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyse',    
					data:'niveau=dep&param='+param,	     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyse',    
					data:'niveau=dep&param='+param, 					     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau=dep&param="+param,page:1}).trigger("reloadGrid");			
			});			
		}
		else if ($.getUrlVar("niveau")==="reg")		// NIVEAU REGION 		
		{
			$regions.on("change",function()
			{
				param="";i=0;
				param+=$sources.val()+","+$("#ana_tour").val()+","+$regions.val();
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau=reg&param="+param,page:1}).trigger("reloadGrid");			
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHistoAnalyse',    
					data:'niveau=reg&param='+param,     					     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieAnalyse',    
					data:'niveau=reg&param='+param,   					     
					success: function(json) {
						$("#chartdiv1").append(json);										
					}    
				});
			});			
		}
		else
		{
			$("#ana_tour").on("change",function()
			{
				param=$sources.val()+","+$("#ana_tour").val();
				if (! $.getUrlVar("niveau") ) {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?param='+param;}
	
				$("#list").setGridParam({url:$url,page:1}).trigger("reloadGrid");
			});
		}
		
		$('#menu-css a').each(function(){
			if($(this).text()!=$('#menu-css a:first').text())
			$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));		
		});
});