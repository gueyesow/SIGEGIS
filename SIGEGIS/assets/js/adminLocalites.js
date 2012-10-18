$(document).ready(function() {
	var $anneeDecoupage=$("#anneeDecoupage");
		var localite={
				"pays":{
					1:"idPays",
					2:"nomPays",
					3:"anneeDecoupage"
				},
				"region":{
					1:"idRegion",
					2:"nomRegion",
					3:"idPays"
				},
				"departement":{
					1:"idDepartement",
					2:"nomDepartement",
					3:"idRegion"
				},
				"collectivite":{
					1:"idCollectivite",
					2:"nomCollectivite",
					3:"idDepartement"
				},
				"centre":{
					1:"idCentre",
					2:"nomCentre",
					3:"idCollectivite"
				}
		};
		
		$localite=$.getUrlVar("typeLocalite");
	
	
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID','Nom','Dépend de'],
	    colModel :[
		{name: localite[$localite][1], index: localite[$localite][1], editable:true},
		{name:localite[$localite][2], index:localite[$localite][2], editable:true},
		{name:localite[$localite][3], index:localite[$localite][3], editable:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: localite[$localite][1],
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true});

	if($localite!="pays") element=$localite+"s";
	else element=$localite;
	
	$.ajax({            
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getDecoupages',            			         			   
		dataType: 'json',      
		success: function(json) {
			$("#anneeDecoupage").empty();
			$.each(json, function(index, value) {         
				$("#anneeDecoupage").append('<option value="'+ index +'">'+ value +'</option>');							
			});	
			$("#anneeDecoupage>:last").attr("selected","selected");
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridLocalites?typeLocalite="+$.getUrlVar("typeLocalite")+"&annee="+$anneeDecoupage.val(), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/localiteCRUD", page:1}).trigger("reloadGrid");								
		}           
	});
	$("#anneeDecoupage").on("change", function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridLocalites?typeLocalite="+$.getUrlVar("typeLocalite")+"&annee="+$anneeDecoupage.val(), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/localiteCRUD", page:1}).trigger("reloadGrid");
	});

	
	
	
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});