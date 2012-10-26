$(document).ready(function() {
	$("#menu ul li:not(':first,:gt(7)')").hide();
	var grid=$("#list");
	
	grid.jqGrid({
		autowidth:true,					
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Candidat', 'Photo', 'Prénom', 'Nom', 'Date de naissance','Lieu de naissance', 'Parti', 'Commentaires'],
	    colModel :[
		{name:'idCandidature', index:'idCandidature', editable:true},
		{name:'photo', index:'photo', edittype: 'image', hidden:true, width:150, editable:true, editrules:{edithidden:true}, editoptions: {src: ''}},
		{name:'prenom', index:'prenom', editable:true, editrules:{required:true}},
		{name:'nom', index:'nom', editable:true, editrules:{required:true}},
		{name:'dateNaissance', index:'dateNaissance', editable:true},
		{name:'lieuNaissance', index:'lieuNaissance', editable:true},
		{name:'partis', index:'partis', editable:true, editrules:{required:true}, edittype:'textarea', editoptions:{rows:"5",cols:"40"}},
		{name:'commentaires', index:'commentaires', width:150, editable:true, edittype:'textarea', editoptions:{rows:"10",cols:"90",class:'ckeditor'}}		
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idCandidature',
	    editurl:"http://www.sigegis.ugb-edu.com/admin_controller/candidatCRUD",
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true, 	    
	    ondblClickRow: function(id) 	{
	    	grid.editGridRow(id,{closeAfterEdit:true,width:700,closeAfterEdit:true,
				recreateForm: true,top:"5",left:"200",
				beforeInitData: function () {
			        var cm = grid.jqGrid('getColProp', 'photo');
			        selRowId = grid.jqGrid('getGridParam', 'selrow');
			        cm.editoptions.src = 'http://www.sigegis.ugb-edu.com/assets/images/candidats/c_' + selRowId + '.jpg';	      			        
				}});
		}
	}).navGrid("#pager",
			{edit:true,add:true,del:true,search:true},
			{
				closeAfterEdit:true,width:700,recreateForm: true,
				beforeInitData: function () {
			        var cm = grid.jqGrid('getColProp', 'photo');
			        selRowId = grid.jqGrid('getGridParam', 'selrow');
			        cm.editoptions.src = 'http://www.sigegis.ugb-edu.com/assets/images/candidats/c_' + selRowId + '.jpg';
				}
			},{closeAfterAdd:true, width:700});

	$centres.on("change",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCandidats?typeElection=presidentielle&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	
	$("#allCandidats").on("click",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCandidats?annee=all",page:1}).trigger("reloadGrid");
	});
	
	$("#notAllCandidats").on("click",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCandidats?typeElection=presidentielle&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	

	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#types_affichage input").attr("disabled","disabled");
});