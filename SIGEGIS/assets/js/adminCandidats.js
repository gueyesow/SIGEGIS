$(document).ready(function() {
	$("#list").jqGrid({
		autowidth:true,			
		url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCandidats",
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Candidat', 'Prénom', 'Nom', 'Date de naissance','Lieu de naissance', 'Parti', 'Commentaires', 'Photo'],
	    colModel :[
		{name:'idCandidature', index:'idCandidature', editable:true},
		{name:'prenom', index:'prenom', editable:true},
		{name:'nom', index:'nom', editable:true},
		{name:'dateNaissance', index:'dateNaissance', editable:true},
		{name:'lieuNaissance', index:'lieuNaissance', editable:true},
		{name:'parti', index:'parti', editable:true},
		{name:'commentaires', index:'commentaires', editable:true},
		{name:'photo', index:'photo', editable:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idCandidature',
	    editurl:"http://www.sigegis.ugb-edu.com/admin_controller/candidatCRUD",
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true});

	$(".ui-jqgrid-bdiv").removeAttr("style");
});