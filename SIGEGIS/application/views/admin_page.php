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
			<tr><th>Elections, candidats, listes et uploads</th><th>Localités</th><th>Résultats</th><th>Participation</th></tr>
			<tr>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editSources")?>">Sources</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editElections?type=presidentielle")?>">Elections</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editCandidats")?>">Candidats</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editListes")?>">Partis et coalitions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editUsers")?>">Gestion des droits</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/upload")?>">Gestion des images</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=pays")?>">Pays</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=region")?>">Régions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=departement")?>">Départements</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=collectivite")?>">Collectivites</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=centre")?>">Centres</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			</tr>
			</table>						
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
