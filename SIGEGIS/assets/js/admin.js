$(document).ready(function() {
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID résultat','Voix','Valide','ID élection','ID source','ID candidat','ID centre','ID département'],
	    colModel :[
		{name:'idResultat', index:'idResultat', editable:true},
		{name:'nbVoix', index:'nbVoix', editable:true},
		{name:'valide', index:'valide', editable:true},
		{name:'idElection', index:'idElection', editable:true},
		{name:'idSource', index:'idSource', editable:true},
		{name:'idCandidature', index:'idCandidature',editable:true}, 
	    {name:'idCentre', index:'idCentre',editable:true},
	    {name:'idDepartement', index:'idDepartement',editable:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idResultat',
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true});

	$centres.on("change",function(){
		param=$sources.val()+","+$elections.val();
		if($.getUrlVar("type")==="presidentielle") param+=","+$tours.val();
		param+=','+$centres.val();
		param+='&typeElection='+$.getUrlVar("type");
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/updateGrid?param="+param, editurl:"http://www.sigegis.ugb-edu.com/admin_controller/editRP?typeElection="+$.getUrlVar("type"), page:1}).trigger("reloadGrid");
		
		parametres_analyse='param='+$sources.val();
		if($.getUrlVar("type")=="presidentielle") parametres_analyse+=","+$tours.val();
		parametres_analyse+=","+$centres.val()+"&annees="+$elections.val()+"&niveau=cen&typeElection="+$.getUrlVar("type");
		
		$.ajax({        							    
			url: "http://www.sigegis.ugb-edu.com/main_controller/getCandidatsAnnee",
			data:parametres_analyse,
			dataType:'json',        					     
			success: function(json) {
				$("#listeCandidats").html('<tr><th>Prénom</th><th>Nom</th></tr>');
				$.each(json, function(index, value) {
					$("#listeCandidats").append('<tr><td>'+ index +'</td><td>'+ value +'</td></tr>');						     
				});																				         
			}    
		});
	});
	
	
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});