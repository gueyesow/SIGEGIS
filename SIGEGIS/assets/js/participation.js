/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE
 * Description: Cette partie gère tout ce qui est Taux de participation - Niches électorales 
 */

$(document).ready(function() {
$("#menu li a").removeClass("selected");
$("#menu_stats>a").addClass("selected");

$("#grid").attr("checked","checked");

function refreshAll(){
	
	param=$sources.val()+","+$elections.val();
	if(type=="presidentielle") param+=","+$tours.val();
	
	switch (niveau) {
	 case "cen":param+=","+$centres.val();break;
	 case "dep":param+=","+$departements.val();break;
	 case "reg":param+=","+$regions.val();break;
	 case "pays":param+=","+$pays.val();break;
	 default:break;
	}
	
	$urlGrid=base_url+'analyser/getGridParticipation?niveau='+niveau+'&param='+param+'&typeElection='+type+'&g='+$GRANULARITE[$elections.val()];
	
	$paramCharts='param='+param+'&typeElection='+type;
	if (niveau!="globaux") $paramCharts+='&niveau='+niveau;
	$paramCharts+='&g='+$GRANULARITE[$elections.val()];
	
	$("#list").setGridParam({url:$urlGrid,page:1}).trigger("reloadGrid");
	chart1.showLoading('<div style="margin:auto;margin-top:150px;">En cours de chargement...<br/><img src="../../assets/images/ajax-loader.gif" width="128px" /></div>');
					
	$.ajax({        							
		url: base_url+'analyser/getComboParticipation',    
		data:$paramCharts,     					     
		success: function(json) {
			refreshComboChart(json);						
		}    
	});
	
	chart2.showLoading('<div style="margin:auto;margin-top:150px;">En cours de chargement...<br/><img src="../../assets/images/ajax-loader.gif" width="128px" /></div>');
	$.ajax({        							
		url: base_url+'analyser/getPoidsElectoralRegions',    
		data:'annee='+$elections.val()+'&tour='+$tours.val()+'&typeElection='+type+'&g='+$GRANULARITE[$elections.val()],   					     
		success: function(json) {
			 $("#chartdiv2").show();
			refreshPiePoidsElectoralRegions(json);			
		}    
	});
}

/**
 * Le diagramme représentant les taux de participation
 */
chart1 = new Highcharts.Chart({
    chart: {
        renderTo: 'chartdiv1',
        height: 560
    },
    title: {
        text: 'Taux de participation'
    },
    xAxis: {
        categories: ['Inscrits', 'Votants', 'Nuls', 'Suffrages exprimés']
    },
    yAxis: {
		min: 0,
		title: {
			text: 'Nombre'
		}
	},
    tooltip: {
        formatter: function() {
            var s;
            if (this.point.name) { // the pie chart
                s = ''+                    
                '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
            } else {
                s = ''+
                '<b>'+ this.x +'</b>: '+ this.percentage.toFixed(2) +' %';
            }
            return s;
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
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			size: 190,
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
    labels: {
        items: [{
            style: {
                left: '40px',
                top: '8px',
                color: 'black'
            }
        }]
    },
    exporting: {
		url:base_url+'assets/js/highcharts/exporting-server/index.php'
	},
	credits: {
        text: 'SIGeGIS.COM',
        href: 'http://www.sigegis2.ugb-edu.com'
	},
    series: []
});

/**
 * Le diagramme représentant le poids électoral des régions
 */
chart2 = new Highcharts.Chart({
	chart: {
		renderTo: 'chartdiv2',
		height: 420	
	},
	title: {
		text: 'Poids électoral des régions'
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
		url:base_url+'assets/js/highcharts/exporting-server/index.php'
	},
	credits: {
        text: 'SIGeGIS.COM',
        href: 'http://www.sigegis2.ugb-edu.com'
	},
	series: []
});

/**
 * Charge les données dans l'objet chart1 (Taux de participation)
 */
function refreshComboChart(json){
		var i=0;
		
		var series=JSON.parse(json);			
		chart1.setTitle({text: series[0].titre},{text: series[0].sous_titre});		
		if ( chart1.series.length > 0 ) {			
			for(i=0;i<chart1.series.length;i++) {chart1.series[i].setData(series[i+1].data,false);}			
		}		
		else	
		{
			for(i=0;i<series.length;i++)
				chart1.addSeries(series[i+1],false);
		}	
		chart1.redraw();
		chart1.hideLoading();
}


/**
 * Charge les données dans l'objet chart2 (Répartition géographique des électeurs)
 */
function refreshPiePoidsElectoralRegions(json){
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
	chart2.hideLoading();
}	

if($.getUrlVar("map")=="no") {$("#gbox_list").hide("animated");} else {$("#gbox_list").show("animated");}
if($.getUrlVar("bar")=="no") {$("#chartdiv1").hide("animated");$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")=="yes") {$("#chartdiv1").show("animated");$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")=="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")=="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");}
if($.getUrlVar("grid")=="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")=="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}

$("#chartdiv2").hide();

$("#pie, #line,#map").attr("disabled","disabled");

$("#types_affichage input").on("change",function() 
{

	var idmode;

	$("#types_affichage input").each(function(){
		idmode=""+$(this).attr("id");
		valeur=($(this).attr("checked")=="checked")?"yes":"no";		
		mode+="&"+idmode+"="+valeur;			
	});
	
	
	if(niveau) mode+="&niveau="+niveau;
	
	if( $.getUrlVar("year") ) {		
		if( $.getUrlVar("year")==$elections.val() )
			mode+="&year="+$.getUrlVar("year");
		else 
			mode+="&year="+$elections.val();
	}					

	window.location=base_url+"analyser/participation?type="+type+mode;
});



$.each(types_election,function(){  
	if (type==""+this){
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
				if (mode) window.location=base_url+"analyser/participation?type="+this+mode;
				else window.location=base_url+"analyser/participation?type="+this;				
			}
			
			$("#ss_locales :input").on("click",function(){
				if (mode) window.location=base_url+"analyser/participation?type="+$(this).attr("id")+mode;
				else window.location=base_url+"analyser/participation?type="+$(this).attr("id");
			});
		}
	});
});
		
if (type != "presidentielle") $("#filtretours").remove();

if (niveau=="cen"){}
else if (niveau=="dep"){$("#filtrecollectivites").remove();$("#filtrecentres").remove();}
else if (niveau=="reg"){$("#filtredepartements").remove();$("#filtrecollectivites").remove();$("#filtrecentres").remove();}
else if (niveau=="pays"){$("#filtreregions").remove();$("#filtredepartements").remove();$("#filtrecollectivites").remove();$("#filtrecentres").remove();}
else {$("#filtrepays").remove();$("#filtreregions").remove();$("#filtredepartements").remove();$("#filtrecollectivites").remove();	$("#filtrecentres").remove();}

$("#list").jqGrid({		
autowidth:true,
datatype: 'xml',
mtype: 'GET',
colNames:['Lieu de vote','Inscrits','Votants','Nuls','Exprimés','Abstentions'],
colModel :[ 
  {name:'nomLieu', index:'nomLieu'}, 
  {name:'nbInscrits', index:'nbInscrits', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}},
  {name:'nbVotants', index:'nbVotants', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}},
  {name:'nbBulletinsNuls', index:'nbBulletinsNuls', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}},
  {name:'nbExprimes', index:'nbExprimes', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}},
{name:'nbAbstention', index:'nbAbstention', width:80,formatter:'number', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}}
],
pager: '#pager',
rowNum:20,
rowList:[20,30,50,100],
sortname: 'nomLieu',
sortorder: 'desc',
    viewrecords: true,
    gridview: true,
}).navGrid("#pager",{edit:false,add:false,del:false,search:false});

$(".ui-jqgrid-bdiv").removeAttr("style");

// Un rafraichissement est operé seulement si le niveau actuel correspond au filtre manipulé 
$centres.on("change",function(){if (niveau=="cen") refreshAll();});
$departements.on("change",function(){if (niveau=="dep") refreshAll();});
$regions.on("change",function(){if (niveau=="reg") refreshAll();});
$pays.on("change",function(){if (niveau=="pays") refreshAll();});
if(niveau=="globaux")  
{
	if (type=="presidentielle") $tours.on("change",function(){refreshAll();});
	else $elections.on("change",function(){refreshAll();});
}

$('#imprimer').on("click",function(){
	window.print();
});

$('#csv').on("click",function(){
	window.location=base_url+"analyser/exportStatisticsToCSV?param="+param+"&typeElection="+type+"&niveau="+niveau+'&g='+$GRANULARITE[$elections.val()]+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
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
	Highcharts.exportCharts([chart1],{type: 'application/pdf'});
});

$('#poidsElectoralRegions').on("click",function(){
	 slideSmoothly("#chartdiv2");
});
});
	