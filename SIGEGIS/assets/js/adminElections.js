$(document).ready(function() {
	$("#left-sidebar button").attr("disabled","disabled");
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Election','Date','Type','Tour','Découpage administratif'],
	    colModel :[
		{name:'idElection',index:'idResultat',editable:true},
		{name:'dateElection',index:'dateElection', formatter:'date', formatoptions: {srcformat:'ISO8601Short', newformat:'d/m/Y'},editable:true,editrules:{required:true}},
		{name:'typeElection',index:'typeElection',edittype:'select',editable:true,editoptions:{value:"presidentielle:Présidentielle;legislative:Législative;regionale:Régionale;municipale:Municipale;rurale:Rurale"},editrules:{required:true}},
		{name:'tour',index:'tour',editable:true,edittype:'select',editoptions:{value:"premier_tour:Premier tour;second_tour:Second tour; :Unique"}},
		{name:'anneeDecoupage',index:'anneeDecoupage',editable:true,editrules:{required:true}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idElection',
	    sortorder: 'asc',	    
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true,closeOnEscape:true,afterShowForm:function(){$("#dateElection").datepicker();}},{closeAfterAdd:true});
		},
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true,closeOnEscape:true,afterShowForm:function(){$("#dateElection").datepicker();}},{closeAfterAdd:true});

	$elections.on("change",function(){		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$("#types_elections input:checked").attr("id"),editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD",page:1}).trigger("reloadGrid");		
	});
	$tours.on("change",function(){		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$("#types_elections input:checked").attr("id"),editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD",page:1}).trigger("reloadGrid");
	});
		
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
		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$("#types_elections input:checked").attr("id"),editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD",page:1}).trigger("reloadGrid");

		//------------------ 	END   	--------------------//
		$("#ss_locales :input").on("click",function(){
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$(this).attr("id"),editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD",page:1}).trigger("reloadGrid");
		});
	});
		
	$("#allListes").on("click",function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection=all",page:1}).trigger("reloadGrid");
	});
	
	$("#notAllListes").on("click",function(){
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridElections?typeElection="+$("#types_elections input:checked").attr("id"),editurl:"http://www.sigegis.ugb-edu.com/admin/electionCRUD",page:1}).trigger("reloadGrid");
	});
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});