$(document).ready(function() {
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$("#types_affichage input").attr("disabled","disabled");
	if(!type) $("#zone_des_filtres select").attr("disabled","disabled");
	
	$("#"+type).attr("checked","checked");
	
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID résultat','Voix','ID élection','ID source','ID candidat','ID centre','ID département'],
	    colModel :[
		{name:'idResultat', index:'idResultat', editable:true},
		{name:'nbVoix', index:'nbVoix', editable:true, editrules:{required:true}},
		{name:'idElection', index:'idElection', editable:true, editrules:{required:true}},
		{name:'idSource', index:'idSource', editable:true, editrules:{required:true}},
		{name:'idCandidat', index:'idCandidat',editable:true, editrules:{required:true}}, 
	    {name:'idCentre', index:'idCentre',editable:true, editrules:{required:true}},
	    {name:'idDepartement', index:'idDepartement',editable:true, editrules:{required:true}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idResultat',
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
		if(niveau=="cen") param+=','+$centres.val();
		else param+=','+$departements.val();
		param+="&niveau="+niveau;
		
			if ($(this).attr("id")=="locale" && !$("#ss_locales").length)
				$("#types_elections").append(
					"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
					"<input id='municipale' type='radio' name='radio2' checked='checked' /><label for='municipale'>Municipales</label><br />"+
					"<input id='regionale' type='radio' name='radio2' /><label for='regionale'>Régionales</label><br />"+
					"<input id='rurale' type='radio' name='radio2' /><label for='rurale'>Rurales</label></fieldset>");	

			if ($("#locale")[0].checked){
				$("#ss_locales").show();
				$("#ss_locales :checked").removeAttr("checked");
			} 
						
			$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridResultats?param="+param+"&typeElection="+$("#types_elections input:checked").attr("id"), editurl:"http://www.sigegis.ugb-edu.com/admin/resultatCRUD?typeElection="+type, page:1}).trigger("reloadGrid");

		});
	
	$("#ss_locales :input").on("click",function(){
		$centres.change();
	});

	$centres.on("change",function(){
		
		param=$sources.val()+","+$elections.val();
		if(type=="presidentielle") param+=","+$tours.val();
		if(niveau=="cen") param+=','+$centres.val();
		else param+=','+$departements.val();
		param+='&typeElection='+type;
		param+="&niveau="+niveau;

		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin/getGridResultats?param="+param, editurl:"http://www.sigegis.ugb-edu.com/admin/resultatCRUD?typeElection="+type, page:1}).trigger("reloadGrid");
		
		if (type!="presidentielle" && type!="legislative") {
			$("#locale").click();
			$("#"+type).attr("checked","checked");						
		}
		if(type!="presidentielle") $("#filtretours").remove();
		$("#types_elections input:not('#"+type+"')").attr("disabled","disabled");
	});
	
	$elections.on("change",function(){
		$centres.change();
	});

	$("*[id*='button_e']").on("click",function(){
		window.location="http://www.sigegis.ugb-edu.com/admin/editResultats?type="+$(this).attr("id").substring(8)+"&niveau="+niveau;
	});
	$("*[id*='button_centre']").on("click",function(){
		window.location="http://www.sigegis.ugb-edu.com/admin/editResultats?type="+type+"&niveau=cen";
	});
	$("*[id*='button_departement']").on("click",function(){
		window.location="http://www.sigegis.ugb-edu.com/admin/editResultats?type="+type+"&niveau=dep";
	});
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});