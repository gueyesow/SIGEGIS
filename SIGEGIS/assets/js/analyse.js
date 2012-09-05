/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie analyse  
 */

$(document).ready(function() {		
	
	chart1 = new Highcharts.Chart({
	chart: {
	renderTo: 'chartdiv1',
	type: 'column'
	},
	title: {
	text: ''
	},
	subtitle: {
	text: ''
	},
	xAxis: {
	categories: [],

	labels: {
	rotation: -40,
	align: 'right',
	style: {
	width:20,
	fontSize: '12px',
	fontFamily: 'Verdana, sans-serif'
	}
	}
	},
	yAxis: {
	min: 0,
	title: {
	text: 'NbVoix'
	}
	},
	exporting: {
	url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
	},
	legend: {
	layout: 'vertical',
	backgroundColor: '#FFFFFF',
	align: 'right',
	verticalAlign: 'top',
	floating: true,
	shadow: true
	},
	tooltip: {
	formatter: function() {
	return  this.y;
	}
	},

	plotOptions: {
	column: {
	pointPadding: 0.2,
	borderWidth: 0,
	dataLabels: {
	enabled: true
	}
	}
	},
	credits: {
	enabled: false
	},
	series:[]
	});		
	
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
					
		/*$.ajax({        							
			url: 'http://www.sigegis.ugb-edu.com/main_controller/analyser',    
			data:"type="+$.getUrlVar("type")+mode, 					     
			success: function(json) {
				if( $("#types_affichage input:eq(1)").attr("checked")!="checked" ) $("#chartdiv1").hide();	else $("#chartdiv1").show();
				if( $("#types_affichage input:eq(2)").attr("checked")!="checked" ) $("#chartdiv2").hide();	else $("#chartdiv2").show();
				if( $("#types_affichage input:eq(3)").attr("checked")!="checked" ) $("#theGrid").hide();	else $("#theGrid").show();
			}    
		});*/
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

});
