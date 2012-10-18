$(document).ready(function() {
	$("#list").jqGrid({
		autowidth:true,			
		url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis",
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Liste', 'Nom', 'Type de liste', 'Partis membres', 'infosComplementaires', 'Photo'],
	    colModel :[
		{name:'idListe', index:'idListe', editable:true},
		{name:'nomListe', index:'nomListe', editable:true},
		{name:'typeListe', index:'typeListe', editable:true},
		{name:'partisMembres', index:'partisMembres', editable:true},
		{name:'infosComplementaires', index:'infosComplementaires', editable:true},
		{name:'logo', index:'logo', editable:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idListe',
	    editurl:"http://www.sigegis.ugb-edu.com/admin_controller/listeCRUD",
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true});

	$(".ui-jqgrid-bdiv").removeAttr("style");
});