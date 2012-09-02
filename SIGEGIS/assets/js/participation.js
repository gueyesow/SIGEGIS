/**
 * Auteurs: Amadou SOW & Abdou Khadre GUEYE
 * Description: Cette partie gère tout ce qui est Taux de participation - Niches électorales 
 */

$(document).ready(function() {   	 
$("#chartdiv2").show();
if($.getUrlVar("map")==="no") {$("#gbox_list").hide("animated");} else {$("#gbox_list").show("animated");}
if($.getUrlVar("bar")==="no") {$("#chartdiv1").hide("animated");$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")==="yes") {$("#chartdiv1").show("animated");$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")==="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")==="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");}
if($.getUrlVar("grid")==="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")==="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}


$("#types_affichage input").on( "change",function() {
	var idmode;

	$("#types_affichage input").each(function(){
		idmode=""+$(this).attr("id");
		valeur=($(this).attr("checked")==="checked")?"yes":"no";		
		mode+="&"+idmode+"="+valeur;			
	});
	
	
	if($.getUrlVar("niveau")) mode+="&niveau="+$.getUrlVar("niveau");
	if( $.getUrlVar("year") ) {		
		if( $.getUrlVar("year")===$elections.val() )
			mode+="&year="+$.getUrlVar("year");
		else 
			mode+="&year="+$elections.val();
	}					

	window.location="http://www.sigegis.ugb-edu.com/main_controller/participation?type="+$.getUrlVar("type")+mode;
});


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
				if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/participation?type="+this+mode;
				else window.location="http://www.sigegis.ugb-edu.com/main_controller/participation?type="+this;				
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
		$("#filtrepays").remove();$("#filtreregions").remove();	$("#filtredepartements").remove();	$("#filtrecollectivites").remove();	$("#filtrecentres").remove();
	}
	

	param=$sources.val()+","+$elections.val();
	if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/getGridParticipation?niveau='+$.getUrlVar("niveau")+'&param='+param+'&typeElection='+$.getUrlVar("type");
	
	$("#list").jqGrid({		
		autowidth:true,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Lieu de vote','Inscrits','Votants','Nuls','Exprimés','Abstention'],
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
	
		if ($.getUrlVar("niveau")==="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function()
			{				
				param=$sources.val()+","+$elections.val();
				if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
				param+=","+$centres.val();				
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridParticipation?niveau=cen&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarParticipation',    
					data:'niveau=cen&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
					success: function(json) {
						$("#chartdiv1").append(json);									
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieParticipation',    
					data:'niveau=cen&param='+param+'&typeElection='+$.getUrlVar("type"),    					     
					success: function(json) {
						$("#chartdiv2").append(json);									
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
						
						$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridParticipation?niveau=dep&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarParticipation',    
							data:'niveau=dep&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
							success: function(json) {
								$("#chartdiv1").append(json);								
							}    
						});
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieParticipation',    
							data:'niveau=dep&param='+param+'&typeElection='+$.getUrlVar("type"),      					     
							success: function(json) {
								$("#chartdiv2").append(json);									
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
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGridParticipation?niveau=reg&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarParticipation',    
					data:'niveau=reg&param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						$("#chartdiv1").append(json);						
					}    
				});
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieParticipation',    
					data:'niveau=reg&param='+param+'&typeElection='+$.getUrlVar("type"),   					     
					success: function(json) {
						$("#chartdiv2").append(json);
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
	
				$url='http://www.sigegis.ugb-edu.com/main_controller/getGridParticipation?param='+param+'&typeElection='+$.getUrlVar("type");
				
				$("#list").setGridParam({url:$url,page:1}).trigger("reloadGrid");
								
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getBarParticipation',    
					data:'param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						$("#chartdiv1").append(json);						
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPieParticipation',    
					data:'param='+param+'&typeElection='+$.getUrlVar("type"),   					     
					success: function(json) {
						$("#chartdiv2").append(json);										
					}    
				});
			});
		}
		
		$('#imprimer').on("click",function(){
			window.print();
		});

		$('#csv').on("click",function(){
			window.location="http://www.sigegis.ugb-edu.com/main_controller/exportStatisticsToCSV?param="+param+"&typeElection="+$.getUrlVar("type")+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
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
		    if ($("#bar").attr("checked")==="checked" && $("#pie").attr("checked")==="checked") Highcharts.exportCharts([chart1,chart2,chart3],{
		    	url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php',type: 'application/pdf'});
			else if ($("#bar").attr("checked")==="checked" || $("#pie").attr("checked")==="checked"){
				if($("#bar").attr("checked")==="checked") Highcharts.exportCharts([chart1],{type: 'application/pdf'});
				else Highcharts.exportCharts([chart2,chart3],{type: 'application/pdf'});				
			}
			else return;
		});
		
		$('#menu-css a').each(function(){
			if($(this).text()!=$('#menu-css a:first').text() && $.getUrlVar("bar"))
			$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));
		});
		
		$('#poidsElectoralRegions').click(function() {
			$.ajax({        							
				url: 'http://www.sigegis.ugb-edu.com/main_controller/getPoidsElectoralRegions',    
				data:'annee='+$elections.val()+'&tour='+$tours.val()+'&typeElection='+$.getUrlVar("type"),   					     
				success: function(json) {
					$("#chartdiv4").append(json);
					window.location.href="#chartdiv4";
				}    
			});
		});	
				
});
