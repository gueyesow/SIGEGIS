<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>My First Grid</title>
<link rel="stylesheet" type="text/css" media="screen"
	href="<?php echo css_url("ui.jqgrid"); ?>" />
<link rel="stylesheet" type="text/css" media="screen"
	href="<?php echo css_url("theme"); ?>" />
<link rel="stylesheet" type="text/css" media="screen"
	href="<?php echo css_url("ui-lightness/jquery-ui-1.8.21.custom"); ?>" />
<script src="<?php echo js_url("jqgrid/js/jquery-1.7.2.min");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/plugins/jquery.searchFilter");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-en");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>"
	type="text/javascript"></script>
<!--script src="<?php echo js_url("jquery-ui/js/jquery-1.7.2.min");?>" type="text/javascript"></script-->
<script
	src="<?php echo js_url("jquery-ui/js/jquery-ui-1.8.21.custom.min");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>
</head>

<body>
<a href="admin_controller/logout">Logout</a>

	<input id="month" name="month" />

	<?php $styles="";?>
	<?php $filtres=array("sources","elections");?>
	<?php $labels_filtres=array("sources"=>"Source","elections"=>"Election");?>

	<div class="zone_des_filtres">
		<form id="drag">
			<?php 
			foreach ($filtres as $filtre)
				echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
			echo "<div style='clear:both;'></div>";
			?>
		</form>
	</div>
	<br />
	<div class="zone_des_filtres">
		<form style="border: 1px solid;" id="form" method="post" action="">
			<table>
				<tr>
					<?php 		
					echo "<td>".form_dropdown("idCandidat0","idCandidat0",$styles,"ID Candidat")."</td>";
					echo "<td><label for='nomCandidat0'>Nom du candidat</label><br /><input type='text' id='nomCandidat0' name='nom0' /></td>";
					?>
					<td valign="bottom"><input class="add" type="button" value="Add" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<br />
	<br />

	<script type="text/javascript">
$(function() {
	var $sources = $('#sources');
	var $elections = $('#elections');
	
	$.ajax({            
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getSources',           			         			      
		dataType: 'json',      
		success: function(json) {
			$.each(json, function(index, value) {         
				$sources.append('<option value="'+ index +'">'+ value +'</option>');     
			});
			$sources.val("1");         
		}       
	});	       
	 
	$.ajax({            
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getDatesElections',           			         			      
		dataType: 'json',      
		success: function(json) {
			$.each(json, function(index, value) {         
				$elections.append('<option value="'+ index +'">'+ value +'</option>');     
			});
			$elections.val("1");         
		}       
	});

	$("input[id*=nomCandidat]").each(
			function(){
				$(this).on("keyup change",function(){
		val1 = $(this).val();      
		if(val1 != '') {            
			$("input[id*=idCandidat0]").empty();           
			$.ajax({            
				url: 'http://www.sigegis.ugb-edu.com/main_controller/getCandidats',
				data:'nomCandidat='+val1,       			         			
				dataType: 'json',      
				success: function(json) {      
					$.each(json, function(index, value) {         
					$("input[id*=idCandidat]").append('<option value="'+ index +'">'+ value +'</option>');     
					});         
				}           
			});       
		}    
	});
	});
	
	var c=1;	
    
	$(":button").on('click', function() 
	{
		$("table")
		.append(
		"<tr><td><div style='float:left;margin:2px;'><label for='idCandidat"+c+"'>ID Candidat</label><br /><select id='idCandidat"+c+"' name='c"+c+"'><option value=''>Id Candidat</option></select></div></td>"+
		"<td><div style='float:left;margin:2px;'><label for='nomCandidat"+c+"'>Nom Candidat</label><br /><input id='nomCandidat"+c+"' type='text' name='nom"+c+"' /></div></td>"+
		"<td valign='bottom'><input class='add' type='button' value='Add' /></td></tr>"
		);
		$("input[id^=nomCandidat]").autocomplete({source: 'http://www.sigegis.ugb-edu.com/main_controller/getCandidatsArray'});					    					
		c++;
	});

	$(".zone_des_filtres").addClass("ui-state-default ui-corner-all");
});

$("#nomCandidat0").autocomplete({source: 'http://www.sigegis.ugb-edu.com/main_controller/getCandidatsArray',select: function(event, ui) { 
    alert(ui.item.id); 
} 
});
//$("#nomCandidat0").autocomplete({source: 'http://www.sigegis.ugb-edu.com/main_controller/getCandidatsArray'});


</script>

</body>
</html>
