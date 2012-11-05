/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie analyse  
 */

$(document).ready(function() {		
	
	// Mise en forme - Activation des Options
	
	$("#menu ul li:gt(0)").remove();
	
	$(".zone_des_options *, #comparer").attr("disabled","disabled");
	
	if(!$("#bar")[0].checked || $("#bar")[0].disabled) {$("*[id*='chartdiv1'],*[id*='chartdiv2']").hide("animated");}
	
	if(!$("#line")[0].checked || $("#line")[0].disabled) {$("*[id*='chartdiv3'],*[id*='chartdiv4']").hide("animated");}
	
	/**
	 * Diagramme N°1: diagramme par défaut (colonnes)
	 */
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
	enabled: false
	},
	series:[]
	});		
	
	/**
	 * Diagramme N°2: courbes
	 */
	chart2 = new Highcharts.Chart({
		chart: {
		renderTo: 'chartdiv3',
		type: 'line'
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
		text: 'Voix'
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
			enabled: false
		},
		series:[]
		});
		
	/**
	 * CHOIX DU MODE DE REPRESENTATION DES DONNEES
	 */
	var numberOfClickForLine=0;
	var numberOfClickForGrid=0;	
	$("#types_affichage input").on( "change",function() {									
		if(!$("#bar")[0].checked) {$("*[id*='chartdiv1'],*[id*='chartdiv2']").hide("animated");} else  if($("#bar")[0].checked) {$("*[id*='chartdiv1']").show("animated");if(save) $("*[id*='chartdiv2']").show("animated");}
		if(!$("#line")[0].checked) {$("*[id*='chartdiv3'],*[id*='chartdiv4']").hide("animated");} else  if($("#line")[0].checked) { $("*[id='chartdiv3']").show("animated"); if(save && $("#chartdiv4").text()!="") $("*[id='chartdiv4']").show("animated");  if (numberOfClickForLine==0) {$("#"+lastPressedButton).click();numberOfClickForLine++;}/*Recharger les charts*/}
		if(!$("#grid")[0].checked) {$("*[id*='theGrid']").hide("animated");} else  if($("#grid")[0].checked) {$("*[id*='theGrid']").show("animated");if (numberOfClickForGrid==0) {$("#"+lastPressedButton).click();numberOfClickForGrid++;}}		
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
		$("select[name*=ana_localite]").change();
		$pays.change();
		$("#ss_locales :input").on("click",function(){
			typeElection=$(this).attr("id");$("#ana_decoupage,#ana_decoupage_localite").change();
		});
		//------------------ 	END   	--------------------//
		if(!$("#presidentielle")[0].checked) $("#filtretours,#filtreana_tour").hide(); else $("#tours,#ana_tour").show();
	});
	
	if (!$("#locale")[0].checked) $("#elections_locales").hide("animated");
	
	/*
 	$('#imprimer').on("click",function(){
		window.print();
	});

	$('#csv').on("click",function(){
		window.location="http://www.sigegis.ugb-edu.com/main_controller/exportToCSVAnalyse?param="+param+"&typeElection="+typeElection+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
	});
	*/
	
	/**
	 * Create a global getSVG method that takes an array of charts as an argument
	 */
	Highcharts.getSVG = function(charts) {
	    var svgArr = [],
	        top = 0,
	        width = 0;

	    $.each(charts, function(i, chart) {
	        var svg = chart.getSVG();
	        svg = svg.replace('<svg', '<g transform="translate(0,' + top + ')" ');
	        svg = svg.replace('</svg>', '</g>');

	        top += chart.chartHeight;
	        width = Math.max(width, chart.chartWidth);

	        svgArr.push(svg);
	    });

	    return '<svg height="'+ top +'" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>';
	};

	/**
	 * Create a global exportCharts method that takes an array of charts as an argument,
	 * and exporting options as the second argument
	 */
	Highcharts.exportCharts = function(charts, options) {
	    var form;
	    svg = Highcharts.getSVG(charts);

	    // merge the options
	    options = Highcharts.merge(Highcharts.getOptions().exporting, options);

	    // create the form
	    form = Highcharts.createElement('form', {
	        method: 'post',
	        action: options.url
	    }, {
	        display: 'none'
	    }, document.body);

	    // add the values
	    Highcharts.each(['filename', 'type', 'width', 'svg'], function(name) {
	        Highcharts.createElement('input', {
	            type: 'hidden',
	            name: name,
	            value: {
	                filename: options.filename || 'chart',
	                type: options.type,
	                width: options.width,
	                svg: svg
	            }[name]
	        }, null, form);
	    });

	    form.submit();

	    form.parentNode.removeChild(form);
	};

	$('#pdf').click(function() {
		Highcharts.exportCharts([chart1],{type: 'application/pdf'});
	});	
});
