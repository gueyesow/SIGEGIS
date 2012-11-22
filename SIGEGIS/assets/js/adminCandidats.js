$(document).ready(function() {
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$("#left-sidebar *").attr("disabled","disabled");
	var grid=$("#list");
	
	grid.jqGrid({
		autowidth:true,					
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Candidat', 'Photo', 'Prénom', 'Nom', 'Date de naissance','Lieu de naissance', 'Parti', 'Commentaires'],
	    colModel :[
		{name:'idCandidat', index:'idCandidat', editable:true},
		{name:'photo', index:'photo', edittype: 'image', hidden:true, width:150, editable:true, editrules:{edithidden:true}, editoptions: {src: ''}},
		{name:'prenom', index:'prenom', editable:true, editrules:{required:true}},
		{name:'nom', index:'nom', editable:true, editrules:{required:true}},
		{name:'dateNaissance', index:'dateNaissance', formatter:'date', formatoptions: {srcformat:'ISO8601Short', newformat:'d/m/Y'}, editable:true},
		{name:'lieuNaissance', index:'lieuNaissance', editable:true},
		{name:'partis', index:'partis', editable:true, editrules:{required:true}, edittype:'textarea', editoptions:{rows:"5",cols:"40"}},
		{name:'commentaires', index:'commentaires', width:150, hidden:true, editable:true, edittype:'textarea', editoptions:{rows:"10",cols:"90"}, 
		editrules:{
            required:false, 
            edithidden:true
         }} 		
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idCandidat',
	    editurl:"http://www.sigegis.ugb-edu.com/admin/candidatCRUD",
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true, 	    
	    ondblClickRow: function(id) 	{
	    	grid.editGridRow(id,
	    		{
		    		width:700,closeAfterEdit:true,
					recreateForm: true,closeOnEscape:true,
					afterShowForm:function(){
						$("#dateNaissance").datepicker();
						$("#commentaires").ckeditor();
					},
					onClose: function() {
						$('#commentaires').ckeditorGet().destroy();					  
					},
					beforeShowForm: function(form) {
		                  var dlgDiv = $("#editmod" + grid[0].id);
		                  var dlgWidth = dlgDiv.width();
		                  dlgDiv[0].style.top =  "10px";
		                  dlgDiv[0].style.left = Math.round(($(window).width()-dlgWidth)/2) + "px";
		            },
					beforeInitData: function () {
				        var cm = grid.jqGrid('getColProp', 'photo');
				        selRowId = grid.jqGrid('getGridParam', 'selrow');
				        cm.editoptions.src = 'http://www.sigegis.ugb-edu.com/assets/images/candidats/c_' + selRowId + '.jpg';
					}
		        });
		}
	}).navGrid("#pager",
			{edit:true,add:true,del:true,search:true},
			{
				closeAfterEdit:true,width:700,closeAfterEdit:true,
				recreateForm: true,closeOnEscape:true,
				afterShowForm:function(){
					$("#dateNaissance").datepicker();
					$("#commentaires").ckeditor();
				},
				onClose: function() {
					$('#commentaires').ckeditorGet().destroy();					  
				},
				beforeShowForm: function(form) {
	                  var dlgDiv = $("#editmod" + grid[0].id);
	                  var dlgWidth = dlgDiv.width();
	                  dlgDiv[0].style.top =  "10px";
	                  dlgDiv[0].style.left = Math.round(($(window).width()-dlgWidth)/2) + "px";
	            },
			},{closeAfterAdd:true, width:700,onClose: function() {$('#commentaires').ckeditorGet().destroy();}});

	$centres.on("change",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridCandidats?typeElection=presidentielle&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	
	$("#allCandidats").on("click",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridCandidats?annee=all",page:1}).trigger("reloadGrid");
	});
	
	$("#notAllCandidats").on("click",function(){
		grid.setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridCandidats?typeElection=presidentielle&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	

	$(".ui-jqgrid-bdiv").removeAttr("style");
	
});