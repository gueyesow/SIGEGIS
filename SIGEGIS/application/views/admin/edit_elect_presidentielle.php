<table id="editgrid"></table>
<div id="pagered"></div>
<input type="BUTTON" id="bedata" value="Edit Selected" />
<script type="text/javascript">
<!--
jQuery("#editgrid").jqGrid({
	url:'http://www.sigegis.ugb-edu.com/admin_controller/editingPresidentielle?q=1',
	datatype: "xml",
	colNames:['idResultat','Nombre de voix','Valide','ID election','ID Source','ID Candidat', 'ID centre','ID département'],
	colModel:[
	{
		name:'idResultat',index:'idResultat', width:55,editable:false,editoptions:{
			readonly:true,size:10}
	},
	{
		name:'nbVoix',index:'nbVoix', width:80,editable:true,editoptions:{
			size:10}
	},
	{
		name:'valide',index:'valide', width:90,editable:true,editoptions:{
			size:25}
	},
	{
		name:'idElection',index:'idElection', width:60, align:"right",editable:true,editoptions:{
			size:10}
	},
	{
		name:'idSource',index:'idSource', width:60, align:"right",editable:true,editoptions:{
			size:10}
	},
	{
		name:'idCandidature',index:'idCandidature', width:60,align:"right",editable:true,editoptions:{
			size:10}
	},
	{
		name:'idCentre',index:'idCentre',width:55,align:'center',editable:true,edittype:"checkbox",editoptions:{
			value:"Yes:No"}
	},
	{
		name:'idDepartement',index:'idDepartement',width:70, editable: true,edittype:"select",editoptions:{
			value:"FE:FedEx;TN:TNT"}
	}
	],
	rowNum:10,
	rowList:[10,20,30],
	pager: '#pagered',
	sortname: 'id',
	viewrecords: true,
	sortorder: "desc",
	caption:"Editing Example",
	editurl:"someurl.php"
});
$("#bedata").click(function(){
	var gr = jQuery("#editgrid").jqGrid('getGridParam','selrow');
	if( gr != null ) jQuery("#editgrid").jqGrid('editGridRow',gr,{
		height:280,reloadAfterSubmit:false});
		else alert("Please Select Row");
});
//-->
</script>

