<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
<style type="text/css">#content table {width:100%;text-align:left;} #content table td {vertical-align:top;}</style>
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
				
				<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img height="58px" src="../../assets/images/print.png" alt="Imprimer toute la page"/></button><br />
				<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img height="58px" src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button><br />
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img height="58px" src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button><br />
			</td>
			<td id="content">
			<h2>Modifications des données</h2>
			<table>
			<tr><th>Elections, candidats et listes</th><th>Localités</th><th>Résultats</th></tr>
			<tr>
			<td>
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Elections</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Candidats</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Partis et coalitions</a><br />
			</td>
			<td>
			<a id="voir_les_pays" href="<?php echo site_url("admin_controller/editLocalites")?>">Pays</a><br />
			<a id="voir_les_regions" href="<?php echo site_url("admin_controller/editLocalites")?>">Régions</a><br />
			<a id="voir_les_departements" href="<?php echo site_url("admin_controller/editLocalites")?>">Départements</a><br />
			<a id="voir_les_collectivites" href="<?php echo site_url("admin_controller/editLocalites")?>">Collectivites</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Centres</a><br />
			</td>
			<td>
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites?type=presidentielle")?>">Présidentielles</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Législatives</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Municipales</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Régionales</a><br />
			<a id="voir_les_centres" href="<?php echo site_url("admin_controller/editLocalites")?>">Rurales</a><br />
			</td>
			</tr>
			</table>
			<h2>Importer des données</h2>
			<div id="counter"></div>
			<div id="nextIDs">
			<p style="color:brown;">Prochains IDs:<br />
			N° CEN: <b><span id="nextCEN"></span></b><br />			
			N° COL: <b>COL<span id="nextCOL"></span></b><br />
			N° DEP: <b>D<span id="nextDEP"></span></b><br />
			N° REG: <b>R<span id="nextREG"></span></b><br />
			N° PAYS: <b><span id="nextPAYS"></span></b><br />
			</p>
			</div>
			<div id="les_localites"></div>
			
<h1>Importer des fichiers CSV</h1>
<div>
<table border="1" width="100%"><tr><td>ID résultat</td><td>nbVoix</td><td>Valide</td><td>ID élection</td><td>ID source</td><td>ID candidat</td><td>ID centre</td><td>ID département</td><td></td></tr></table>

<label>Date de l'élection</label> <input type="text" name="dateElection" id="date" />&nbsp;&nbsp;
<label>Type élection</label><select><option>Présidentielle</option><option>Législatives</option><option>Régionales</option><option>Municipales</option><option>Rurales</option></select>
<label>Tour</label><select><option>Premier tour</option><option>Second tour</option></select><br/>
<form action="upload/do_upload" enctype="multipart/form-data">
<label for="">Sources</label>
<input type="file" name="importSources" /><br />
<input type="submit" value="Submit">
</form>
<label for="">Pays</label>
<input type="file" name="importPays" /><br />
<label for="">Régions</label>
<input type="file" name="importRegions" /><br />
<label for="">Départements</label>
<input type="file" name="importDepartements" /><br />
<label for="">Collectivités</label>
<input type="file" name="importCollectivites" /><br />
<label for="">Centres</label>
<input type="file" name="importCentres" /><br />
<label for="">Candidats</label>
<input type="file" name="importCandidats" /><br />
<label for="">Bureaux annulés</label>
<input type="file" name="importBueauxAnnules" /><br />
<label for="">Résultats présidentielles</label>
<input type="file" name="importResultatsPresidentielles" /><br />
<label for="">Résultats législatives</label>
<input type="file" name="importResultatsLegislatives" /><br />
<label for="">Résultats régionales</label>
<input type="file" name="importResultatsRegionales" /><br />
<label for="">Résultats municipales</label>
<input type="file" name="importResultatsMunicipales" /><br />
<label for="">Résultats rurales</label>
<input type="file" name="importResultatsRurales" /><br />
</div>
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
