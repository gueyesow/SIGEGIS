<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIGEGIS</title>
<link rel="stylesheet" type="text/css" media="print" href="<?php echo css_url("print"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui.jqgrid"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("theme"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui-lightness/jquery-ui-1.8.21.custom"); ?>" />
<script src="<?php echo js_url("jqgrid/js/jquery-1.7.2.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/plugins/jquery.searchFilter");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-fr");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jquery-ui/js/jquery-ui-1.8.21.custom.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("sources","elections","tours","pays","regions","departements","collectivites","centres");?>
	<?php $labels_filtres=array("sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>
	<?php $typeElection=empty($_GET["type"])?$type:$_GET["type"];?>

	<div id="menu">
		<ul>
			<li><a class="actif" href="<?php echo site_url();?>">Accueil</a></li>			
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=globaux");?>">Résultats globaux</a></li>
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=reg");?>">Résultats régionaux</a></li>
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=dep");?>">Résultats départementaux</a></li>
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=cen");?>">Résultats par centre</a></li>
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=globaux");?>">Statistiques</a></li>
		</ul>
	</div>

	<div id="zone_des_filtres">
		<form>
			<?php 
			foreach ($filtres as $filtre)
				echo $this->mon_filtre->form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
			echo "<div style='clear:both;'></div>";
			?>
		</form>
	</div>
	<br />
	<br />

	<table id="wrapper-table">
		<tr>
			<td id="left-sidebar">
				<div class="zone_des_options">
					<form action="">
						<fieldset id="types_elections">
							<legend>Type d'élection à représenter</legend>
							<input id="presidentielle" type="radio" name="radio" />
							<label for="presidentielle">Election présidentielle</label><br /> 
							<input id="legislative" type="radio" name="radio" />
							<label for="legislative">Election législative</label><br /> 
							<input id="locale" type="radio" name="radio2" />
							<label for="locale">Election locale</label><br />
						</fieldset>

						<fieldset id="types_affichage">
							<legend>Mode de représentation</legend>
							<input type="checkbox" id="map" name="map" />
							<label for="map">Carte</label><br />
							<input type="checkbox" id="bar" checked="checked" name="bar" />
							<label for="bar">Diagramme en colonnes</label><br /> 
							<input type="checkbox" id="pie" checked="checked" name="pie" />
							<label for="pie">Diagramme à secteurs</label><br /> 
							<input type="checkbox" id="grid" name="grid" checked="checked" />
							<label for="grid">Tableau</label>
						</fieldset>

						<fieldset>
							<legend>Format des données</legend>
							<input id="valeur_absolue" type="radio" name="format" checked="checked" />
							<label for="valeur_absolue">Valeurs absolues</label><br />
							<input id="valeur_relative" type="radio" name="format" />
							<label for="valeur_relative">Valeurs relatives</label><br />
						</fieldset>
					</form>
				</div> <br> <br>

				<button id="poidsElectoralRegions" class="theToolTip boutons" title="Afficher les poids des régions">Poids électoral des régions</button><br /><br />
				<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img height="58px" src="../../assets/images/print.png" alt="Imprimer toute la page"/></button>
				<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img height="58px" src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button>
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img height="58px" src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button>
			</td>
			<td id="content">
				<div>

					<h1 id="titre"></h1>

					<div id="theGrid">
						<table id="list"></table>
						<div id="pager"></div>
					</div>

					<div id="chartdiv1" class="diagrammes"></div>
					<div id="chartdiv2" class="diagrammes"></div>

					<br />

					<div style='clear: both;'></div>
				</div>
			</td>
		</tr>
	</table>
	
	<div id="footer">
		<p><a href="<?php echo site_url("main_controller/administration");?>"><img height="14px" alt="administration" src="../../assets/images/lock.png" /></a>&copy; Copyright SIGEGIS | Université Gaston Berger de Saint-Louis du Sénégal | 2012</p>
	</div>
</div>


	<script src="<?php echo js_url("base");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("init_filtres");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("participation");?>" type="text/javascript"></script>	
	<script src="<?php echo js_url("dragAndDrop");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("tooltips");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("style");?>" type="text/javascript"></script>
	
</body>
</html>
