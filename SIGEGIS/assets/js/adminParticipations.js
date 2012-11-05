$(document).ready(function() {

	$("#types_affichage input").attr("disabled","disabled");
	$("#"+type).attr("checked","checked");
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID participation','Inscrits','Votants','Nuls','Exprimés','ID élection','ID source','ID centre','ID département'],
	    colModel :[
		{name:'idParticipation', index:'idParticipation', editable:true},
		{name:'nbInscrits', index:'nbInscrits', editable:true, editrules:{required:true}},
		{name:'nbVotants', index:'nbVotants', editable:true, editrules:{required:true}},
		{name:'nbBulletinsNuls', index:'nbBulletinsNuls', editable:true, editrules:{required:true}},
		{name:'nbExprimes', index:'nbExprimes', editable:true, editrules:{required:true}},
		{name:'idElection', index:'idElection', editable:true, editrules:{required:true}},
		{name:'idSource', index:'idSource', editable:true, editrules:{required:true}}, 
	    {name:'idCentre', index:'idCentre',editable:true, editrules:{required:true}},
	    {name:'idDepartement', index:'idDepartement',editable:true, editrules:{required:true}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idParticipation',
	    sortorder: 'asc',
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true,closeAfterEdit:true});
		},
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true},{closeAfterAdd:true});	
	
$("#types_elections input").on("click",function(){
	param=$sources.val()+","+$elections.val();
	if(type=="presidentielle") param+=","+$tours.val();
	if(niveau=="dep") param+=','+$departements.val();
	
		if ($(this).attr("id")=="locale" && !$("#ss_locales").length)
			$("#types_elections").append(
				"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio2' checked='checked' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio2' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio2' /><label for='rurale'>Rurales</label></fieldset>");	

		if ($("#locale")[0].checked){
			$("#ss_locales").show("animated");
			$("#ss_locales :checked").removeAttr("checked");
		}
		
		//------------------ RELOAD ALL --------------------//
		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridParticipation?param="+param+"&typeElection="+$("#types_elections input:checked").attr("id"), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/participationCRUD?typeElection="+$("#types_elections input:checked").attr("id"), page:1}).trigger("reloadGrid");

		//------------------ 	END   	--------------------//
		$("#ss_locales :input").on("click",function(){
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridParticipation?param="+param+"&typeElection="+$("#ss_locales input:checked").attr("id"), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/participationCRUD?typeElection="+$("#ss_locales input:checked").attr("id"), page:1}).trigger("reloadGrid");
		});		
	});

$("#ss_locales :input").on("click",function(){
	$centres.change();
});

$centres.on("change",function(){	
	param=$sources.val()+","+$elections.val();
	if(type=="presidentielle") param+=","+$tours.val();
	if(niveau=="dep") param+=','+$departements.val();
	param+='&typeElection='+type;
	$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridParticipation?param="+param, editurl:"http://www.sigegis.ugb-edu.com/admin_controller/participationCRUD?typeElection="+$("#types_elections input:checked").attr("id"), page:1}).trigger("reloadGrid");
	
	if (type!="presidentielle" && type!="legislative") {
		$("#locale").click();
		$("#"+type).attr("checked","checked");						
	}
	if(type!="presidentielle") $("#filtretours").remove();
	$("#types_elections input:not('#"+type+"')").attr("disabled","disabled");	
});

$("*[id*='button_e']").on("click",function(){
	window.location="http://www.sigegis.ugb-edu.com/admin_controller/editParticipations?type="+$(this).attr("id").substring(8)+"&niveau="+niveau+"&year="+$elections.val();
});
$("*[id*='button_centre']").on("click",function(){
	window.location="http://www.sigegis.ugb-edu.com/admin_controller/editParticipations?type="+type+"&niveau=cen&year="+$elections.val();
});
$("*[id*='button_departement']").on("click",function(){
	window.location="http://www.sigegis.ugb-edu.com/admin_controller/editParticipations?type="+type+"&niveau=dep&year="+$elections.val();
});
	$(".ui-jqgrid-bdiv").removeAttr("style");

});