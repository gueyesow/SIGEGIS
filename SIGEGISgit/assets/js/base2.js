
$(document).ready(function() {   	 

if($.getUrlVar("map")==="no") {$("#gbox_list").hide("animated");} else {$("#gbox_list").show("animated");}
if($.getUrlVar("bar")==="no") {$("#chartdiv1").hide("animated");$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")==="yes") {$("#chartdiv1").show("animated");$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")==="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")==="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");}
if($.getUrlVar("grid")==="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")==="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}

$("#types_affichage input").on( "change",function() {
	i=0;var idmode;

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
				"<input id='municipale' type='radio' name='radio' checked='checked' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio' /><label for='rurale'>Rurales</label></fieldset>");
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
			if (mode) window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this+mode;
			else window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type="+this;
		}
	});
});
		
	
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
	

	param=$sources.val()+","+$elections.val()+","+$tours.val();
	
	//if ( $.getUrlVar("niveau") ) {$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau='+$.getUrlVar("niveau")+'&param='+param;}
	//else {$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?param='+param;}
	$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau='+$.getUrlVar("niveau")+'&param='+param;

	//alert($url);
	$("#list").jqGrid({		
		//url:$url,
		autowidth:true,
	    datatype: 'xml',
	    mtype: 'GET',
	    colNames:['Nom du candidat','Nombre de voix'],
	    colModel :[ 
	      {name:'nomCandidat', index:'nomCandidat', search:true}, 
	      {name:'nbVoix', index:'nbVoix', width:80, align:'nbVoix',sortable:true}  
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100],
	    sortname: 'nbVoix',
	    sortorder: 'desc',
	    viewrecords: true,
	    gridview: true,
	}).navGrid("#pager",{edit:true,add:true,del:true});

		if ($.getUrlVar("niveau")==="cen") // NIVEAU CENTRE 
		{		
			$centres.on("change",function()
			{				
				param=$sources.val()+","+$elections.val()+","+$tours.val()+","+$centres.val();				
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'niveau=cen&param='+param,        					     
					success: function(json) {
						$("#chartdiv1").append(json);//alert(param);
						$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=cen&param="+param,page:1}).trigger("reloadGrid");										
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
					data:'niveau=cen&param='+param,      					     
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
						
						param=$sources.val()+","+$elections.val()+","+$tours.val()+","+$departements.val();				
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
							data:'niveau=dep&param='+param,        					     
							success: function(json) {
								$("#chartdiv1").append(json);
								$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=dep&param="+param,page:1}).trigger("reloadGrid");
							}    
						});
						
						$.ajax({        							
							url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
							data:'niveau=dep&param='+param,      					     
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
				param=$sources.val()+","+$elections.val()+","+$tours.val()+","+$regions.val();											
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'niveau=reg&param='+param,     					     
					success: function(json) {
						$("#chartdiv1").append(json);
						$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/main_controller/getGrid?niveau=reg&param="+param,page:1}).trigger("reloadGrid");
					}    
				});
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
					data:'niveau=reg&param='+param,   					     
					success: function(json) {
						$("#chartdiv2").append(json);
					}    
				});
			});			
		}
		else if($.getUrlVar("niveau")==="globaux")  
		{
			$pays.on("change",function(){
				
				param=$sources.val()+","+$elections.val()+","+$tours.val()+",null";
	
				$url='http://www.sigegis.ugb-edu.com/main_controller/getGrid?param='+param;
				
								
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getHisto',    
					data:'&param='+param,     					     
					success: function(json) {
						$("#chartdiv1").append(json);
						$("#list").setGridParam({url:$url,page:1}).trigger("reloadGrid");
					}    
				});
				
				$.ajax({        							
					url: 'http://www.sigegis.ugb-edu.com/main_controller/getPie',    
					data:'&param='+param,   					     
					success: function(json) {
						$("#chartdiv2").append(json);										
					}    
				});
			});
		}  			
		
});
