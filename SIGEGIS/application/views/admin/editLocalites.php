<?php if(!$this->session->userdata('logged_in')) show_error("ACCES NON AUTORISE");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("anneeDecoupage");?>
	<?php $labels_filtres=array("anneeDecoupage"=>"Année de découpage");?>	

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
			<div id="radio">
				        <input type="radio" id="button_pays" name="radio" />
				        <label for="button_pays">Pays</label>
				        <input type="radio" id="button_region" name="radio" />
				        <label for="button_region">Régions</label>
				        <input type="radio" id="button_departement" name="radio" />
				        <label for="button_departement">Départements</label>
				        <input type="radio" id="button_collectivite" name="radio" />
				        <label for="button_collectivite">Collectivités</label>
				        <input type="radio" id="button_centre" name="radio" />
				        <label for="button_centre">Centres</label>
				    </div><br /><br />
			<h1>LOCALITES</h1>
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
