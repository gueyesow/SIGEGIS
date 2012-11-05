/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE 
 * Description: Gestion de la partie d'affichage des résultats    
 */

$(document).ready(function() {
	
	// Mise en forme - Activation des Options
	
	$("#line").attr("disabled","disabled");

	// Charge les données dans le bar chart
	
	function showPopUp(json){
		objet = jQuery.parseJSON(json);            						
		output="<div id='popup'>";
		output+="<img src='http://www.sigegis.ugb-edu.com/assets/images/candidats/c_"+objet.idPhoto+".jpg' style='float:left;margin:10px;' alt='Photo' />";
		output+="<b>Prénom: </b>"+objet.prenom+"<br />";
		output+="<b>Nom: </b>"+objet.nom+"<br />";
		output+="<b>Date de naissance: </b>"+objet.dateNaissance+"<br />";
		output+="<b>Lieu de naissance: </b>"+objet.lieuNaissance+"<br />";
		output+="<b>Détails: </b><br />"+objet.contenu;
		output+="</div>";
		$("#contenu_modale").html(output);
		$('#fenetre').dialog('open');            					 
	    return false;	
	}
	
	function refreshAll(){				
		param=$sources.val()+","+$elections.val();
		if(type=="presidentielle") param+=","+$tours.val();
		
		switch (niveau) {
		 case "cen":param+=","+$centres.val();break;
		 case "dep":param+=","+$departements.val();break;
		 case "reg":param+=","+$regions.val();break;
		 case "pays":param+=","+$pays.val();break;
		 case "globaux":break;
		 default:break;
		}
		
		$urlGrid="http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau="+niveau+"&param="+param+"&typeElection="+type;
		
		$paramCharts='param='+param+'&typeElection='+type;
		if (niveau!="globaux") $paramCharts+='&niveau='+niveau;
		
		$("#list").setGridParam({url:$urlGrid,page:1}).trigger("reloadGrid");
		
		$.ajax({        							
			url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarVisualiser',    
			data:$paramCharts,        					     
			success: function(json) {		
				refreshBarChart(json);																						
			}    
		});
		
		$.ajax({        							
			url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieVisualiser',    
			data:$paramCharts,    					     
			success: function(json) {
				refreshPieChart(json);
			}    
		});
	}
	 
	function refreshBarChart(json){
		var series = {            
	            name: 'Résultats',
	            data: []
	    };
		
		categories=new Array();
		
		data=JSON.parse(json);
		$titre=data.titre;
		$sous_titre=data.sous_titre;
		if(niveau!="globaux") $sous_titre+=" | ";
		$sous_titre+="Source: "+$("#sources :selected").text();
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
	
	// Charge les données dans le pie chart
	
	function refreshPieChart(json){
		var i=0;
		
		var series=JSON.parse(json);			
		$sous_titre=series[0].sous_titre;
		if(niveau!="globaux") $sous_titre+=" | ";
		$sous_titre+="Source: "+$("#sources :selected").text();
		chart2.setTitle({text: series[0].titre},{text: $sous_titre});		
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
	
	// Création de l'objet chart1
    
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
			},
			series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                        	$.ajax({        							
            					url: this.options.url,
            					success: function(json) {    
            						showPopUp(json);
            					}    
            				});                            
                        }
                    }
                }
            }
		},
		credits: {
			enabled: false
		},
		series: []
	});
	
	// Création de l'objet chart2
	
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
		},
		series: {
            cursor: 'pointer',
            point: {
                events: {
                    click: function() {
                    	$.ajax({        							
        					url: this.options.url,
        					success: function(json) {    
        						showPopUp(json);
        					}    
        				});                            
                    }
                }
            }
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


if(! $.getUrlVar("type"))	
{
	$("#left-sidebar input, #left-sidebar button, #zone_des_filtres select").attr("disabled","disabled");
	$("#chartdiv1").hide();
	
	$("#menu ul li:lt(7):not(:first)").remove();
}
else {$("#help").remove(); $("#chartdiv1").show();$("#menu ul li:gt(6)").remove();}

	
if($.getUrlVar("map")=="no") {$("#gbox_list").hide();} else {$("#gbox_list").show();}
if($.getUrlVar("bar")=="no") {$("#chartdiv1").hide();$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")=="yes") {$("#chartdiv1").show();$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")=="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")=="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");} else $("#chartdiv2").hide();
if($.getUrlVar("grid")=="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")=="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}

// Prise en compte des paramètres d'affichage (Bar,Pie,Map,Grid,Line)

$("#types_affichage input").on("change",function() 
{
	var idmode;	// variable qui designe l'un des modes de representation des donnees et valeur sa valeur

	$("#types_affichage input").each(function(){
		idmode=""+$(this).attr("id");
		valeur=($(this).attr("checked")=="checked")?"yes":"no";		
		mode+="&"+idmode+"="+valeur;			
	});
	
	if(niveau) {
		if ($(this).attr("id")=="map") {
			if (niveau!="reg" && niveau!="dep") mode+="&niveau=dep";
			else mode+="&niveau="+niveau;
		} else mode+="&niveau="+niveau;
	}
	
	if(!$.getUrlVar("year")) mode+="&year="+$elections.val();
	else {
		if($.getUrlVar("year")==$elections.val())
		mode+="&year="+$.getUrlVar("year"); else mode+="&year="+$elections.val(); 
	}

	if ($(this).attr("id")=="map")
		window.location="http://www.sigegis.ugb-edu.com/main_controller/map?type="+$.getUrlVar("type")+mode;
	else
	window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+$.getUrlVar("type")+mode;
	
	$("#ss_locales :input").on("click",function(){
		if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this+mode;
		else window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this;
	});
});

// Affiner les options pour les élections locales (regionales,municipales,rurales)

$.each(types_election,function(){  
	if ($.getUrlVar("type")==""+this){
		$("#"+this).attr("checked","checked");
		if(""+this=="locale"||""+this=="municipale"||""+this=="regionale"||""+this=="rurale") {
			$("#types_elections").append(
				"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio3' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio3' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio3' /><label for='rurale'>Rurales</label></fieldset>");
			$("#locale").attr("checked","checked");$("#"+this).attr("checked","checked");
		}
	}
});

// Prise en compte du changement du type d'élection

$("#types_elections input").on( "change",function() {
	var idelection=""+$(this).attr("id");
	
	$.each(types_election,function(){		
		if (idelection==""+this )
		{
			
			$("#types_affichage input").each(function(){
				idmode=""+$(this).attr("id");
				valeur=($(this).attr("checked")=="checked")?"yes":"no";		
				mode+="&"+idmode+"="+valeur;	
			});
			
			if(niveau) mode+="&niveau="+niveau;
			
			if(  this !="regionale" && this!="municipale" && this!="rurale" ) {
				if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this+mode;
				else window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this;				
			}
			
			$("#ss_locales :input").on("click",function(){
				if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+$(this).attr("id")+mode;
				else window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+$(this).attr("id");
			});
		}
	});
});
		
if ($.getUrlVar("type") != "presidentielle") $("#filtretours").remove();

	if (niveau=="cen")
	{

	}
	else
	if (niveau=="dep")
	{
		$("#filtrecollectivites").remove();
		$("#filtrecentres").remove();
	}
	else
	if (niveau=="reg")
	{
		$("#filtredepartements").remove();
		$("#filtrecollectivites").remove();
		$("#filtrecentres").remove();
	}
	else
	if (niveau=="pays")
	{
		$("#filtreregions").remove();
		$("#filtredepartements").remove();
		$("#filtrecollectivites").remove();
		$("#filtrecentres").remove();
	}
	else
	{
		$("#filtrepays").remove();$("#filtreregions").remove();	$("#filtredepartements").remove();	$("#filtrecollectivites").remove();	$("#filtrecentres").remove();
	}
	

	param=$sources.val()+","+$elections.val();
	
	if($.getUrlVar("type")=="presidentielle") param+=","+$tours.val();
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/getGridVisualiser?niveau='+niveau+'&param='+param+'&typeElection='+$.getUrlVar("type");
	
	if ( $.getUrlVar("type") && $("#grid")[0].checked ) 
	$("#list").jqGrid({		
		autowidth:true,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Nom du candidat','Voix','% exprimés','Source'],
	    colModel :[ 
	      {name:'nomCandidat', index:'nomCandidat',search:true}, 
	      {name:'nbVoix', index:'nbVoix', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}},
	      {name:'pourcentage', index:'pourcentage', width:80,formatter:'number', formatoptions:{decimalPlaces: 2}},
	      {name:'nomSource', index:'nomSource', width:80, search:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100],
	    sortname: 'nbVoix',
	    sortorder: 'desc',
	    viewrecords: true,
	    gridview: true,
	}).navGrid("#pager",{edit:false,add:false,del:false,search:false});
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
	
		if (niveau=="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function(){refreshAll();});				
		}
		else if (niveau=="dep")	// NIVEAU DEPARTEMENT 
		{		
			$departements.on("change",function(){refreshAll();});			
		}
		else if (niveau=="reg")		// NIVEAU REGION 		
		{
			$regions.on("change",function(){refreshAll();});
		}
		else if (niveau=="pays")		// NIVEAU REGION 		
		{
			$pays.on("change",function(){refreshAll();});
		}
		else if(niveau=="globaux")  
		{
			if ($.getUrlVar("type")=="presidentielle") $tours.on("change",function(){refreshAll();});
			else $elections.on("change",function(){refreshAll();});
		}
		
		$('#imprimer').on("click",function(){
			window.print();
		});

		$('#csv').on("click",function(){
			if (niveau) paramNiveau="&niveau="+niveau;else paramNiveau="";
			if( $("#grid")[0].checked )
			window.location="http://www.sigegis.ugb-edu.com/main_controller/exportResultatsToCSV?param="+param+"&typeElection="+$.getUrlVar("type")+"&sord="+$("#list").jqGrid('getGridParam','sortorder')+paramNiveau;
			else
				window.location="http://www.sigegis.ugb-edu.com/main_controller/exportResultatsToCSV?param="+param+"&typeElection="+$.getUrlVar("type")+"&sord=desc"+paramNiveau;
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
			if ($("#bar")[0].checked && $("#pie")[0].checked) Highcharts.exportCharts([chart1,chart2],{type: 'application/pdf'});
			else if ($("#bar")[0].checked || $("#pie")[0].checked){
				if($("#bar")[0].checked) theCharts=chart1;
				else theCharts=chart2;
				Highcharts.exportCharts([theCharts],{type: 'application/pdf'});
			}
			else return;
		});
		
		$('#visualiser').click(function() {
			window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type=presidentielle&niveau=globaux";
		});
		
		$('#analyser').click(function() {
			window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser";			
		});
				
		  $('#fenetre').dialog({
		    autoOpen: false,
		    bgiframe: true,
		    resizable: true,
		    modal: true,
		    width: 700,
		    height: 500,
		    show: 'fade',
		    title: 'Présentation du candidat', buttons: { Fermer: function() { $( this ).dialog( "close" ); } }
		  });		  
		  $('#menu li a:not(#menu_front a,#menu_exemples a,#menu_apropos a)').each(function(){
				if($(this).text()!=$('#menu a:first').text() && $.getUrlVar("bar"))
					$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));
			});
});
