<?php if(!$this->session->userdata('logged_in')) show_error("ACCES NON AUTORISE");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("sources","elections","tours","pays","regions","departements","collectivites","centres");?>
	<?php $labels_filtres=array("sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>
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
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button>
			</td>
			<td id="content">
			<form>
			<table width="100%">
			<tr>
			<td>
				    <div id="radio">
				        <input type="radio" id="button_epresidentielle" name="radio" />
				        <label for="button_epresidentielle">Présidentielles</label>
				        <input type="radio" id="button_elegislative" name="radio" />
				        <label for="button_elegislative">Législatives</label>
				        <input type="radio" id="button_eregionale" name="radio" />
				        <label for="button_eregionale">Régionales</label>
				        <input type="radio" id="button_emunicipale" name="radio" />
				        <label for="button_emunicipale">Municipales</label>
				        <input type="radio" id="button_erurale" name="radio" />
				        <label for="button_erurale">Rurales</label>
				    </div></td><td>
				    <div id="radio2">
				    	<input type="radio" id="button_centre" name="radio2"/>
				        <label for="button_centre">Niveau centre</label>
				        <input type="radio" id="button_departement" name="radio2" />
				        <label for="button_departement">Niveau département</label>
				    </div></td></tr></table>
				</form><br /><br />
				<h1>TAUX DE PARTICIPATION</h1>
				<h3 id="titre"></h3>
				<div id="theGrid">
					<table id="list"></table>
					<div id="pager"></div>
				</div>				
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
