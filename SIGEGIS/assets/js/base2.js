
$(document).ready(function() {   	 

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

	window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+$.getUrlVar("type")+mode;
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
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau='+$.getUrlVar("niveau")+'&param='+param+'&typeElection='+$.getUrlVar("type");
	
	$("#list").jqGrid({		
		autowidth:true,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Nom du candidat','Nombre de voix'],
	    colModel :[ 
	      {name:'nomCandidat', index:'nomCandidat'}, 
	      {name:'nbVoix', index:'nbVoix', width:80,formatter:'currency', formatoptions:{thousandsSeparator: " ", decimalPlaces: 0}}  
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100],
	    sortname: 'nbVoix',
	    sortorder: 'desc',
	    viewrecords: true,
	    gridview: true,
	}).navGrid("#pager",{edit:false,add:false,del:false});
	
	$(".ui-jqgrid-bdiv").attr("style","min-height:150px");
	
		if ($.getUrlVar("niveau")==="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function()
			{				
				param=$sources.val()+","+$elections.val();
				if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
				param+=","+$centres.val();				
				
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=cen&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'niveau=cen&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
					success: function(json) {
						$("#chartdiv1").append(json);									
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
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
						
						$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=dep&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
							data:'niveau=dep&param='+param+'&typeElection='+$.getUrlVar("type"),        					     
							success: function(json) {
								$("#chartdiv1").append(json);								
							}    
						});
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
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
				$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=reg&param="+param+"&typeElection="+$.getUrlVar("type"),page:1}).trigger("reloadGrid");
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'niveau=reg&param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						$("#chartdiv1").append(json);						
					}    
				});
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
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
	
				$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?param='+param+'&typeElection='+$.getUrlVar("type");
				
				$("#list").setGridParam({url:$url,page:1}).trigger("reloadGrid");
								
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'&param='+param+'&typeElection='+$.getUrlVar("type"),     					     
					success: function(json) {
						$("#chartdiv1").append(json);						
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
					data:'&param='+param+'&typeElection='+$.getUrlVar("type"),   					     
					success: function(json) {
						$("#chartdiv2").append(json);										
					}    
				});
			});
		}
		
		$('#imprimer').on("click",function(){
			window.print();
		});

		$('#exportcsv').on("click",function(){
			window.location="http://www.sigegis.ugb-edu.com/main_controller/exportToCSV?param="+param+"&typeElection="+$.getUrlVar("type")+"&sord="+$("#list").jqGrid('getGridParam','sortorder');
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
		    Highcharts.exportCharts([chart1, chart2]);
		});
		
		$('#menu-css a').each(function(){
			if($(this).text()!=$('#menu-css a:first').text())
			$(this).attr("href",$(this).attr("href")+"&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid"));
		});
});
