$(document).ready(function() {
	$("#menu ul li:not(':first,:gt(7)')").hide();
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Liste', 'Nom', 'Type de liste', 'Partis membres', 'Infos Complémentaires', 'Logo'],
	    colModel :[
		{name:'idListe', index:'idListe', editable:true},
		{name:'nomListe', index:'nomListe', editable:true, editrules:{required:true}},
		{name:'typeListe', index:'typeListe', editable:true, editrules:{required:true}},
		{name:'partis', index:'partis', edittype:'textarea', editoptions:{rows:"4",cols:"60"}, editable:true},
		{name:'infosComplementaires', index:'infosComplementaires', editable:true, edittype:'textarea', editoptions:{rows:"10",cols:"90"}},
		{name:'logo', index:'logo', edittype:'image', editable:true, hidden:true, editrules:{edithidden:true}, editoptions: {src: '', width:150}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idListe',
	    editurl:"http://www.sigegis.ugb-edu.com/admin_controller/listeCRUD",
	    sortorder: 'asc',	    
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true,width:700,closeAfterEdit:true,
				recreateForm: true,top:"5",left:"200",
				beforeInitData: function () {
			        var cm = $("#list").jqGrid('getColProp', 'logo');
			        selRowId = $("#list").jqGrid('getGridParam', 'selrow');
			        cm.editoptions.src = 'http://www.sigegis.ugb-edu.com/assets/images/partis/pc_' + selRowId + '.jpg';	      			        
				}});
		},
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true},{closeAfterAdd:true});

	$centres.on("change",function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis?typeElection="+$("#types_elections input:checked").attr("id")+"&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	$elections.on("change",function(){$("#ss_locales :checked").removeAttr("checked");});
	
	$("#allListes").on("click",function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis?annee=all",page:1}).trigger("reloadGrid");
	});
	
	$("#notAllListes").on("click",function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis?typeElection="+$("#types_elections input:checked").attr("id")+"&annee="+$elections.val(),page:1}).trigger("reloadGrid");
	});
	

	$(".ui-jqgrid-bdiv").removeAttr("style");
	$("#types_affichage input").attr("disabled","disabled");
	$("#types_elections input").on("click",function(){
		
		if ($(this).attr("id")=="locale" && !$("#ss_locales").length)
			$("#types_elections").append(
				"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio2' checked='checked' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio2' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio2' /><label for='rurale'>Rurales</label></fieldset>");	

		if ($("#locale")[0].checked){
			$("#ss_locales").show("animated");
			$("#ss_locales :checked").removeAttr("checked");
		} else $("#ss_locales").hide("animated");
		
		//------------------ RELOAD ALL --------------------//
		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis?typeElection="+$("#types_elections input:checked").attr("id")+"&annee="+$elections.val(),page:1}).trigger("reloadGrid");

		//------------------ 	END   	--------------------//
		$("#ss_locales :input").on("click",function(){
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridCoalitionsPartis?typeElection="+$("#ss_locales input:checked").attr("id")+"&annee="+$elections.val(),page:1}).trigger("reloadGrid");
		});
	});
	$("#presidentielle").attr("disabled","disabled");
	$("#legislative").attr("checked","checked");
});