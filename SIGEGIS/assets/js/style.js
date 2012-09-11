
$("input[id*='valider'],input[id*='validerLocalite'], #connection_form :submit").button();

$("#ouvrir, #comparer").css("width","200px");

$('#menu li a').hover(   
		  
function() {     
	$(this).css('padding', '5px 15px')   
         .stop()
         .animate({'backgroundColor':'rgba(0,0,0,0.5)'},'fast');               
    },   
    function() {   
    	$(this).css('padding', '5px 15px')   
         .stop()   
         .animate({'paddingLeft'    : '15px',   
                    'paddingRight'      : '15px',   
                    'backgroundColor' :'rgba(0,0,0,0.2)'},   
                    'fast');   
  
    }).mousedown(function() {   
  
    $(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.1)'}, 'fast');   
  
    }).mouseup(function() {   
  
        $(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.5)'}, 'fast');   
    });   
  
	$("#zone_des_filtres").addClass("ui-state-default ui-corner-all");
	$(".zone_des_options").addClass("ui-state-default ui-corner-all");
	
	$("#accordion").accordion({ header: "h3" });
	$("#accordion2").accordion({ header: "h3" });

	$("#dialog_zone_des_options").dialog({
		autoOpen: false,
		width: 800,
		buttons: {
			"Fermer": function() {
				$(this).dialog("close");
				$("#ouvrir").show();
			}
		},
		closeOnEscape: true ,
		resizable: false,
		beforeClose: function(event, ui) { $("#ouvrir").show(); }
	});
	
	
	$("#ouvrir").on("click",function(){
		$("#dialog_zone_des_options").dialog('open');
		$("#ouvrir").hide();
		$(".zone_des_options *:not(*[id='bar'],*[id='pie'])").removeAttr("disabled");
		$("#comparer").removeAttr("disabled");
		typeElection=$(".zone_des_options input:checked:not(input[id=locale])").attr("id");
	});
	
	$("#comparer").on("click",function(){
		save=true;
		if(save) {balise="chartdiv2";if($("#line")[0].checked) baliseLine="chartdiv4";}
		else {balise="chartdiv1";if($("#line")[0].checked) baliseLine="chartdiv3";}
		
		$("#chartdiv2").show();
		if ($("#line")[0].checked) $("#chartdiv4").show();
		
		$("#dialog_zone_des_options").dialog('open');

		chart1 = new Highcharts.Chart({
			chart: {
			renderTo: balise,
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
		
		chart2 = new Highcharts.Chart({
			chart: {
			renderTo: baliseLine,
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
	});
	
	$(".boutons").button({
        icons: {
            secondary: "ui-icon-gear"            
        },
        text: true
    });
	