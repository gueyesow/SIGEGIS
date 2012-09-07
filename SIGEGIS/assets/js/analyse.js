/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie analyse  
 */

$(document).ready(function() {		
	$(".zone_des_options *").attr("disabled","disabled");
	
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
	
	
	/**
	 * CHOIX DU MODE DE REPRESENTATION DES DONNEES
	 */
	
	$("#types_affichage input").on( "change",function() {									
		if(!$("#bar")[0].checked) {$("*[id*='chartdiv']").hide("animated");$("#bar").removeAttr("checked");} else  if($("#bar")[0].checked) {$("*[id*='chartdiv']").show("animated");$("#bar").attr("checked","checked");}
		if(!$("#grid")[0].checked) {$("*[id*='theGrid']").hide("animated");$("#grid").removeAttr("checked");} else  if($("#grid")[0].checked) {$("*[id*='theGrid']").show("animated");$("#grid").attr("checked","checked");}
	});
	
	
	/**
	 * REDEFINITION DES VALEURS DES BOUTONS RADIOS (SELECTIONNER L'OPTION D'AFFICHAGE CHOISIE)
	 */	
	$("#types_elections input").on("click",function(){
		if ($("#locale")[0].checked){
			$("#elections_locales").show("animated");
		} else $("#elections_locales").hide("animated");
		//------------------ RELOAD ALL --------------------//
		$("*[id*='choix']").empty();
		Annees();
		$("select[name*=ana_localite]").change();
		$pays.change();
		//------------------ 	END   	--------------------//
	});
	if (!$("#locale")[0].checked) $("#elections_locales").hide("animated");
	
	$('#imprimer').on("click",function(){
		window.print();
	});

	$('#csv').on("click",function(){
		window.location="http://www.sigegis.ugb-edu.com/main_controller/exportToCSVAnalyse?param="+param+"&typeElection="+typeElection+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
	});
	
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
		/*if ($("#bar").attr("checked")==="checked" && $("#pie").attr("checked")==="checked") Highcharts.exportCharts([chart1,chart2],{type: 'application/pdf'});
		else if ($("#bar").attr("checked")==="checked" || $("#pie").attr("checked")==="checked"){
			if($("#bar").attr("checked")==="checked") theCharts=chart1;
			else theCharts=chart2;
			Highcharts.exportCharts([theCharts],{type: 'application/pdf'});
		}
		else return;*/
		Highcharts.exportCharts([chart1],{type: 'application/pdf'});
	});
	
	if(!$("#bar")[0].checked || $("#bar")[0].disabled) {$("*[id*='chartdiv']").hide("animated");}

});
