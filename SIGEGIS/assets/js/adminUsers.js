$(document).ready(function() {
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$("#left-sidebar *").attr("disabled","disabled");
	$("#list").jqGrid({
		autowidth:true,
		url:"http://www.sigegis.ugb-edu.com/admin/getGridUsers",
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID','Identifiant','Mot de passe','Nouveau mot de passe','Rang'],
	    colModel :[
		{name:'id', index:'id', editable:true},
		{name:'username', index:'username', editable:true, editrules:{required:true}},
		{name:'oldpassword', index:'password', editable:true, edittype:'password', editrules:{required:true}, editoptions:{size:'30'}},
		{name:'newpassword', index:'password', editable:true, hidden:true, edittype:'password',  editoptions:{size:'30'},editrules:{edithidden:true}},		
		{name:'level', index:'level', editable:true, edittype:'select', editrules:{required:true},editoptions:{value:"1:Admin;2:Opérateur;3:Visiteur"}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'id',
	    sortorder: 'asc',	    
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true});
		},
		editurl:"http://www.sigegis.ugb-edu.com/admin/userCRUD",
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true, width:300},{closeAfterAdd:true});
	
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
		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$("#types_elections input:checked").attr("id"), editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD", page:1}).trigger("reloadGrid");

		//------------------ 	END   	--------------------//
		$("#ss_locales :input").on("click",function(){
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$(this).attr("id"), editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD", page:1}).trigger("reloadGrid");
		});
	});
		
	
	//if (!$("#locale")[0].checked) $("#elections_locales").hide("animated");
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});