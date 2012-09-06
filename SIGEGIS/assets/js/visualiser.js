/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie d'affichage des résultats    
 */

$(document).ready(function() {
	
	function refreshBarChart(json){
		var series = {            
	            name: 'Résultats',
	            data: []
	    };
		
		categories=new Array();
		
		data=JSON.parse(json);
		$titre=data.titre;
		$sous_titre=data.sous_titre;
		$unite=data.unite;
		$abscisse=data.abscisse;
		$ordonnee=data.ordonnee;
		
		$.each($ordonnee, function(value) {							
			categories.push($abscisse[value]);
			series.data.push($ordonnee[value]);
	    });
													
		chart1.xAxis[0].setCategories(categories);					
		chart1.setTitle({text: $titre},{text: $sous_titre});
								
		if ( chart1.series.length > 0 ) {chart1.series[0].setData(series.data,true);} 
		else	
			chart1.addSeries(series);
	}
	
	function refreshPieChart(json){
		var i=0;
		
		var series=JSON.parse(json);			
		chart2.setTitle({text: series[0].titre},{text: series[0].sous_titre});		
		if ( chart2.series.length > 0 ) {			
			for(i=0;i<chart2.series.length;i++) {chart2.series[i].setData(series[i+1].data,false);}			
		}		
		else	
		{
			for(i=0;i<series.length;i++)
				chart2.addSeries(series[i+1],false);
		}	
		chart2.redraw();															
	}
    
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
					enabled:false
				},
				tooltip: {
					formatter: function() {
						return  this.x +': '+ this.y;
					}
				},
				
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0,
						colorByPoint: true,
						dataLabels: {
							enabled: true
						}
					}
				},
				credits: {
					enabled: false
				},
				series: []
			});
			
			chart2 = new Highcharts.Chart({
				chart: {
					renderTo: 'chartdiv2'
				},
				title: {
					text: ''
				},
				subtitle: {
					text: ''
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
					}
				},
				plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					size: 200,
					dataLabels: {
						enabled: true,
						color: '#000000',
						connectorColor: '#000000',
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
						}
					},
					showInLegend: true
				}
					
				},
				exporting: {
					url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
				},
				credits: {
					enabled: false
				},
				series: [{
					type: 'pie',
					name: 'Browser share',
					data: []
				}]
				});


if(! $.getUrlVar("type"))	{$("#left-sidebar input, #left-sidebar button, #zone_des_filtres select").attr("disabled","disabled");}
else $("#help").remove();

	
if($.getUrlVar("map")==="no") {$("#gbox_list").hide("animated");} else {$("#gbox_list").show("animated");}
if($.getUrlVar("bar")==="no") {$("#chartdiv1").hide();$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")==="yes") {$("#chartdiv1").show();$("#bar").attr("checked","checked");} //else $("#chartdiv1").hide();
if($.getUrlVar("pie")==="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")==="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");} else $("#chartdiv2").hide();
if($.getUrlVar("grid")==="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")==="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}

// Prise en compte des paramètres d'affichage (Bar,Pie,Map,Grid)   
$("#types_affichage input").on( "change",function() {
	var idmode;

	$("#types_affichage input").each(function(){
		idmode=""+$(this).attr("id");
		valeur=($(this).attr("checked")==="checked")?"yes":"no";		
		mode+="&"+idmode+"="+valeur;			
	});
	
	
	if($.getUrlVar("niveau")) mode+="&niveau="+$.getUrlVar("niveau");
	
/*	if( $.getUrlVar("year") && $.getUrlVar("year")===$elections.val() )
		mode+="&year="+$.getUrlVar("year");
	else
		mode+="&year="+$elections.val();*/						

	window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+$.getUrlVar("type")+mode;
});

// Affiner les options pour les élections locales (regionales,municipales,rurales)    
$.each(types_election,function(){  
	if ($.getUrlVar("type")===""+this){
		$("#"+this).attr("checked","checked");
		if(""+this==="locale"||""+this==="municipale"||""+this==="regionale"||""+this==="rurale") {
			$("#types_elections").append(
				"<fieldset><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio3' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio3' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio3' /><label for='rurale'>Rurales</label></fieldset>");
			$("#locale").attr("checked","checked");
		}
	}
});

// Prise en compte du changement du type d'élection  
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
			if(  this !="regionale" && this!="municipale" && this!="rurale" ) {
				if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this+mode;
				else window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this;				
			}
			//else alert(this);
		}
	});
});
		
if ($.getUrlVar("type") != "presidentielle") $("#filtretours").remove();

	if ($.getUrlVar("niveau")==="cen")
	{

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
		$("#filtrepays").remove();$("#filtreregions").remove();	$("#filtredepartements").remove();	$("#filtrecollectivites").remove();	$("#filtrecentres").remove();
	}
	

	param=$sources.val()+","+$elections.val();
	if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau='+$.getUrlVar("niveau")+'&param='+param+'&typeElection='+$.getUrlVar("type");
	if ( $.getUrlVar("type")) 
	$("#list").jqGrid({		
		autowidth:true,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Nom du candidat','Nombre de voix'],
	    colModel :[ 
	      {name:'nomCandidat', index:'nomCandidat',search:true}, 
	      {name:'nbVoix', index:'nbVoix', width:80,formatter:'currency', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}}  
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100],
	    sortname: 'nbVoix',
	    sortorder: 'desc',
	    viewrecords: true,
	    gridview: true,
	}).navGrid("#pager",{edit:false,add:false,del:false,search:false});
	
	//$(".ui-jqgrid-bdiv").attr("style","min-height:150px");
	$(".ui-jqgrid-bdiv").removeAttr("style");
	
		if ($.getUrlVar("niveau")==="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function()
			{				
				param=$sources.val()+","+$elections.val();
				if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
				param+=","+$centres.val();				
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau=cen&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarVisualiser',    
					data:'niveau=cen&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
					success: function(json) {		
						refreshBarChart(json);																						
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieVisualiser',    
					data:'niveau=cen&param='+param+'&typeElection='+$.getUrlVar("type"),    					     
					success: function(json) {
						refreshPieChart(json);
					}    
				});							
			});			
		}
		else if ($.getUrlVar("niveau")==="dep")	// NIVEAU DEPARTEMENT 
		{		
			$departements.on("change",function()
			{
						
						param=$sources.val()+","+$elections.val();
						if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
						param+=","+$departements.val();
						
						$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau=dep&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarVisualiser',    
							data:'niveau=dep&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
							success: function(json) {
								refreshBarChart(json);								
							}    
						});
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieVisualiser',    
							data:'niveau=dep&param='+param+'&typeElection='+$.getUrlVar("type"),      					     
							success: function(json) {
								refreshPieChart(json);									
							}    
						});									
			});			
			
		}
		else if ($.getUrlVar("niveau")==="reg")		// NIVEAU REGION 		
		{
			$regions.on("change",function()
			{
				param=$sources.val()+","+$elections.val();
				if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
				param+=","+$regions.val();
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau=reg&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarVisualiser',    
					data:'niveau=reg&param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						refreshBarChart(json);					
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieVisualiser',    
					data:'niveau=reg&param='+param+'&typeElection='+$.getUrlVar("type"),   					     
					success: function(json) {
						refreshPieChart(json);
					}    
				});
			});			
		}
		else if($.getUrlVar("niveau")==="globaux")  
		{
			$pays.on("change",function(){
				
				param=$sources.val()+","+$elections.val();
				if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
				param+=",null";
	
				$url='http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?param='+param+'&typeElection='+$.getUrlVar("type");
				
				$("#list").setGridParam({url:$url,page:1}).trigger("reloadGrid");
								
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarVisualiser',    
					data:'&param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						refreshBarChart(json);					
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieVisualiser',    
					data:'&param='+param+'&typeElection='+$.getUrlVar("type"),   					     
					success: function(json) {
						refreshPieChart(json);									
					}    
				});
			});
		}
		
		$('#imprimer').on("click",function(){
			window.print();
		});

		$('#csv').on("click",function(){
			window.location="http://www.sigegis.ugb-edu.com/main_controller/exportResultatsToCSV?param="+param+"&typeElection="+$.getUrlVar("type")+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
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
			if ($("#bar").attr("checked")==="checked" && $("#pie").attr("checked")==="checked") Highcharts.exportCharts([chart1,chart2],{type: 'application/pdf'});
			else if ($("#bar").attr("checked")==="checked" || $("#pie").attr("checked")==="checked"){
				if($("#bar").attr("checked")==="checked") theCharts=chart1;
				else theCharts=chart2;
				Highcharts.exportCharts([theCharts],{type: 'application/pdf'});
			}
			else return;
		});
		
		$('#menu a').each(function(){
			if($(this).text()!=$('#menu a:first').text() && $.getUrlVar("bar"))
			$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));
		});
		
		$('#visualiser').click(function() {
			window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type=presidentielle&niveau=globaux";
		});
		
		$('#analyser').click(function() {
			window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type=presidentielle&niveau=globaux";			
		});
				
});
