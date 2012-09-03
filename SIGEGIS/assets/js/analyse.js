/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie analyse  
 */

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
	 * Ajouter en paramètre la source,le tour à représenter
	 */
	
	/*param=$sources.val()+","+$("#ana_tour").val();
	if ( $.getUrlVar("niveau") ) {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?niveau='+$.getUrlVar("niveau")+'&param='+param;}
	else {$url='http://www.sigegis.ugb-edu.com/main_controller/analyser?param='+param;}*/

	
	$("#list").jqGrid({		
		//url:$url,
		autowidth:true,
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
	
	/*		
	$('#menu-css a').each(function(){
		if($(this).text()!=$('#menu-css a:first').text() && $.getUrlVar("bar"))
		$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));
	});
	*/
		
});
