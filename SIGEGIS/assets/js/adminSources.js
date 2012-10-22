$(document).ready(function() {
	$("#menu ul li:not(':first,:gt(7)')").hide();
	$("#left-sidebar input, #left-sidebar button, #zone_des_filtres select").attr("disabled","disabled");
	$("#list").jqGrid({
		autowidth:true,
		url:"http://www.sigegis.ugb-edu.com/admin_controller/getGridSources",
	    datatype: 'xml',
	    mtype: 'POST',
	    colNames:['ID source','Nom de la source'],
	    colModel :[
		{name:'idSource', index:'idSource', editable:true},
		{name:'nomSource', index:'nomSource', editable:true, editrules:{required:true}}
	    ],
	    pager: '#pager',
	    rowNum:20,
	    rowList:[5,10,20],
	    sortname: 'idSource',
	    sortorder: 'asc',	    
	    ondblClickRow: function(id) 	{
	    	$("#list").editGridRow(id,{closeAfterEdit:true});
		},
	    viewrecords: true,
	    editurl:"http://www.sigegis.ugb-edu.com/admin_controller/sourceCRUD",
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true},{closeAfterAdd:true});

	$(".ui-jqgrid-bdiv").removeAttr("style");
});