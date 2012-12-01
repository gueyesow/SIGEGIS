$anneeDecoupage=$("#anneeDecoupage");

$(document).ready(function() {
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$("#left-sidebar *").attr("disabled","disabled");
	 
	label={
			"idPays":"ID pays","nomPays":"Nom du pays","anneeDecoupage":"Découpage administratif",
				"idRegion":"ID région",
				"nomRegion":"Nom de la région",
				"idDepartement":"ID département",
				"nomDepartement":"Nom du département",
				"idCollectivite":"ID collectivité",
				"nomCollectivite":"Nom de la collectivité",
				"idCentre":"ID centre",
				"nomCentre":"Nom du centre"					
	};
	
	localite={
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
	    colNames:[label[localite[$localite][1]],label[localite[$localite][2]],label[localite[$localite][3]]],
	    colModel :[
		{name: localite[$localite][1], index: localite[$localite][1], editable:true},
		{name:localite[$localite][2], index:localite[$localite][2], editable:true, search:true, stype:'text', editrules:{required:true}},
		{name:localite[$localite][3], index:localite[$localite][3], editable:true, editrules:{required:true}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: localite[$localite][1],
	    sortorder: 'asc',	    
	    viewrecords: true,
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true});
		},
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true},{closeAfterAdd:true});

	if($localite!="pays") element=$localite+"s";
	else element=$localite;
	
	$.ajax({            
		url: base_url+'filtres/getDecoupages',            			         			   
		dataType: 'json',      
		success: function(json) {
			$anneeDecoupage.empty();
			$.each(json, function(index, value) {         
				$anneeDecoupage.append('<option value="'+ index +'">'+ value +'</option>');							
			});	
			
			if ($.getUrlVar("annee")) $anneeDecoupage.val($.getUrlVar("annee"));
			else $("#anneeDecoupage>:last").attr("selected","selected");
			
			$anneeDecoupage.change();
		}           
	});
	
	$anneeDecoupage.on("change",function(){
		$("#list").setGridParam({url:base_url+"admin/getGridLocalites?typeLocalite="+$.getUrlVar("typeLocalite")+"&annee="+$(this).val(), editurl:base_url+"admin/localiteCRUD?typeLocalite="+$.getUrlVar("typeLocalite")+"&annee="+$anneeDecoupage.val(), page:1}).trigger("reloadGrid");
	});
	
	$("*[id*='button_']").on("click",function(){
		window.location=base_url+"admin/editLocalites?typeLocalite="+$(this).attr("id").substring(7)+"&annee="+$anneeDecoupage.val();
	});
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
	
});
