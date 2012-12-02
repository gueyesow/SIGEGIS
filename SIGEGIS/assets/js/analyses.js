/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie analyse  
 */

/**
 * Diagramme N°1: diagramme par défaut (colonnes)
 */
function putBar(elementConteneur){
	chart1 = new Highcharts.Chart({
	chart: {
	renderTo: elementConteneur,
	type: 'column'
	},
	title: {
	text: 'Données indisponibles'
	},
	subtitle: {
	text: 'Réexécutez la requête !'
	},
	xAxis: {
	categories: [],

	labels: {
	rotation: -40,
	align: 'right',
	style: {
	fontSize: '12px',
	fontFamily: 'Verdana, sans-serif'
	}
	}
	},
	yAxis: {
	min: 0,
	title: {
	text: 'Voix'
	}
	},
	exporting: {
	url: base_url+'assets/js/highcharts/exporting-server/index.php'
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
			return  "<b>"+this.series.name+":</b> "+this.y;
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
        text: 'SIGeGIS.COM',
        href: 'http://www.sigegis2.ugb-edu.com'
	},
	series:[]
	});		
	}

	/*
	 * Diagramme N°2: courbes
	 */
	function putLine(elementConteneur){
	$("#chartdiv3").show(); // Afficher le conteneur pour que le diagramme puisse s'afficher correctement 
	chart2 = new Highcharts.Chart({
		chart: {
		renderTo: elementConteneur,
		type: 'line'
		},
		title: {
		text: 'Données indisponibles'
		},
		subtitle: {
		text: 'Réexécutez la requête !'
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
		text: 'Voix'
		}
		},
		exporting: {
		url: base_url+'assets/js/highcharts/exporting-server/index.php'
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
				return  "<b>"+this.series.name+":</b> "+this.y;
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
            text: 'SIGeGIS.COM',
            href: 'http://www.sigegis2.ugb-edu.com'
		},
		series:[]
		});
	if (!$("#line")[0].checked) $("#chartdiv3,#chartdiv4").hide();
	}	
	
$(document).ready(function() {		
	
	// Mise en forme - Activation des Options
	
	$(".zone_des_options *, #comparer").attr("disabled","disabled");
	
	if(!$("#grid")[0].checked || $("#grid")[0].disabled) {$("#theGrid1,#theGrid2").hide("animated");}
	
	if(!$("#bar")[0].checked || $("#bar")[0].disabled) {$("#chartdiv1,#chartdiv2").hide("animated");}
	
	if(!$("#line")[0].checked || $("#line")[0].disabled) {$("#chartdiv3,#chartdiv4").hide("animated");}
	
	if(!save) {$("#simple").hide("animated");}
	
	putBar("chartdiv1");
	putLine("chartdiv3");
	
	/**
	 * CHOIX DU MODE DE REPRESENTATION DES DONNEES
	 */
	var numberOfClickForLine=0;
	var numberOfClickForGrid=0;	
	
	$("#types_affichage input").on("change",function() {
		
		if(!$("#bar")[0].checked) {
			$("#chartdiv1,#chartdiv2").hide("animated");
		} 
		else  {	$("#chartdiv1").show("animated"); if(save) $("#chartdiv2").show("animated"); }
		
		if(!$("#line")[0].checked) {
			$("#chartdiv3,#chartdiv4").hide("animated");
		} 
		else {
			$("#chartdiv3").show("animated"); 
			if(save) $("#chartdiv4").show("animated");  
			//if (numberOfClickForLine==0) {$("#"+lastPressedButton).click();numberOfClickForLine++;}
		}
		
		if(!$("#grid")[0].checked) {
			$("#theGrid1,#theGrid2").hide("animated");
		} 
		else {
			$("#theGrid1").show("animated");
			if(save) $("#theGrid2").show("animated");
		}
 	});
		
	
	/**
	 * Au changement du type d'élection à représenter, recharger les éléments de formulaire 
	 */	
	$("#types_elections input").on("click",function(){
		typeElection=$(this).attr("id");
		if ($(this).attr("id")=="locale" && !$("#ss_locales").length)
			$("#types_elections").append(
				"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio2' checked='checked' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio2' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio2' /><label for='rurale'>Rurales</label></fieldset>");	

		if ($("#locale")[0].checked){
			$("#ss_locales").show("animated");
			$("#ss_locales :checked").removeAttr("checked");
		} else $("#ss_locales").hide("animated");
		//------------------ RELOAD ALL --------------------//
		$("#ana_decoupage,#ana_decoupage_localite").change();
		$("*[id*='choix']").empty();
		Annees();
		$("select[name*=niveauAgregation]").change();
		$pays.change();
		$("#ss_locales :input").on("click",function(){
			typeElection=$(this).attr("id");$("#ana_decoupage,#ana_decoupage_localite").change();
		});
		//------------------ 	END   	--------------------//
		if(!$("#presidentielle")[0].checked) $("#filtretours,#filtreana_tour").hide(); else $("#tours,#ana_tour").show();
	});
	
	if (!$("#locale")[0].checked) $("#elections_locales").hide("animated");
});
