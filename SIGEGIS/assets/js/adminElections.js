$(document).ready(function() {
	$("#list").jqGrid({
		autowidth:true,			
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID Election','Date','Type','Tour','Découpage administratif'],
	    colModel :[
		{name:'idElection', index:'idResultat', editable:true},
		{name:'dateElection', index:'dateElection', sorttype:'date', formatoptions:{srcformat:"Y-m-d", newformat:"d/m/Y"}, editable:true},
		{name:'typeElection', index:'typeElection', editable:true},
		{name:'tour', index:'tour', editable:true},
		{name:'anneeDecoupage', index:'anneeDecoupage', editable:true}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[20,30,50,100,1000],
	    sortname: 'idElection',
	    sortorder: 'asc',	    
	    viewrecords: true,
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true});

	$elections.on("change",function(){		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridElections?typeElection="+$.getUrlVar("type"), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/electionCRUD", page:1}).trigger("reloadGrid");		
	});
	$tours.on("change",function(){		
		$("#list").setGridParam({url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridElections?typeElection="+$.getUrlVar("type"), editurl:"http://www.sigegis.ugb-edu.com/admin_controller/electionCRUD", page:1}).trigger("reloadGrid");
	});
	
	
	
	$(".ui-jqgrid-bdiv").removeAttr("style");
});