<?php if(!$this->session->userdata('logged_in')) show_error("ACCES NON AUTORISE");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
<style type="text/css">#wrapper #wrapper-table #content table {width:100%;text-align:left;} #content table td {vertical-align:top;}</style>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("anneeDecoupages","sources","elections","tours","pays","regions","departements","collectivites","centres");?>
	<?php $labels_filtres=array("anneeDecoupages"=>"Année découpage","sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>
	<?php $typeElection=empty($_GET["type"])?"presidentielle":$_GET["type"];?>	

	<?php echo $menu;?>		

	<div id="zone_des_filtres">
		<form>
			<?php 
			foreach ($filtres as $filtre)
				echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
			echo "<div style='clear:both;'></div>";
			?>
		</form>
	</div>
	<br />
	<br />

	<table id="wrapper-table">
		<tr>
			<td id="left-sidebar">
			
			<?php echo $options_menu;?>
				
				<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img src="../../assets/images/print.png" alt="Imprimer toute la page"/></button>
				<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button>
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button><br />
			</td>
			<td id="content">
			<h2>Modifications des données</h2>
			<table>
			<tr><th>Elections, candidats et listes</th><th>Localités</th><th>Résultats</th><th>Participation</th></tr>
			<tr>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editSources")?>">Sources</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editElections?type=presidentielle")?>">Elections</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editCandidats")?>">Candidats</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editListes")?>">Partis et coalitions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editUsers")?>">Gestion des droits</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editLocalites?typeLocalite=pays")?>">Pays</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editLocalites?typeLocalite=region")?>">Régions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editLocalites?typeLocalite=departement")?>">Départements</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editLocalites?typeLocalite=collectivite")?>">Collectivites</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editLocalites?typeLocalite=centre")?>">Centres</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editResultats?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editResultats?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editResultats?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editResultats?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editResultats?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editParticipations?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editParticipations?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editParticipations?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editParticipations?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin_controller/editParticipations?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			</tr>
			</table>			
<script type="text/javascript">
$("#date").datepicker({
	showOn: 'button',
	buttonImage: '../../assets/images/calendar.png',
	buttonImageOnly: true
});
				
$(function() {	 
	
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
		"<tr><td><div style='float:left;margin:2px;'><label for='idCandidat"+c+"'>ID Candidat</label><br /><br /><select id='idCandidat"+c+"' name='c"+c+"'><option value=''>Id Candidat</option></select></div></td>"+
		"<td><div style='float:left;margin:2px;'><label for='nomCandidat"+c+"'>Nom Candidat</label><br /><br /><input id='nomCandidat"+c+"' type='text' name='nom"+c+"' /><br /></div></td>"+
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
</script>			
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
