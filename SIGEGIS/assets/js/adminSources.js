$(document).ready(function() {
	$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
	$(":input").attr("disabled","disabled");
	$("#list").jqGrid({
		autowidth:true,
		url:base_url+"admin/getGridSources",
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
	    editurl:base_url+"admin/sourceCRUD",
	    gridview: true
	}).navGrid("#pager",{edit:true,add:true,del:true,search:true},{closeAfterEdit:true},{closeAfterAdd:true});

	$(".ui-jqgrid-bdiv").removeAttr("style");
});
