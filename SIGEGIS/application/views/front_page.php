<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIGEGIS</title>
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
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-fr");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>"
	type="text/javascript"></script>
<script
	src="<?php echo js_url("jquery-ui/js/jquery-ui-1.8.21.custom.min");?>"
	type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>
</head>

<body>
	<?php $styles="";?>
	<?php $filtres=array("sources","elections","tours","pays","regions","departements","collectivites","centres");?>
	<?php $labels_filtres=array("sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>

	<!--div id="header-wrap"-->
	<div id="menu-css">
		<ul>
			<li><a class="actif" href="<?php echo site_url();?>">Accueil</a></li>
			<li><a
				href="<?php echo site_url("main_controller/administration");?>">Administration</a>
			</li>
			<li><a
				href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=globaux");?>">Résultats
					globaux</a></li>
			<li><a
				href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=reg");?>">Résultats
					régionaux</a></li>
			<li><a title="yes"
				href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=dep");?>">Résultats
					départementaux</a></li>
			<li><a class="location" title="yes"
				href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=cen");?>">Résultats
					au niveau des centres</a></li>
		</ul>
	</div>

	<div id="zone_des_filtres">
		<form id="drag">
			<?php 
			foreach ($filtres as $filtre)
				echo $this->mon_filtre->form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
			echo "<div style='clear:both;'></div>";
			?>
		</form>
	</div>
	<br />
	<br />

	<table id="tableau">
		<tr>
			<td id="left-sidebar">
				<div class="zone_des_options">
					<form action="">
						<fieldset id="types_elections">
							<legend>Type d'élection à représenter</legend>
							<input id="presidentielle" type="radio" name="radio" /><label
								for="presidentielle">Election présidentielle</label><br /> <input
								id="legislative" type="radio" name="radio" /><label
								for="legislative">Election législative</label><br /> <input
								id="locale" type="radio" name="radio2" /><label for="locale">Election
								locale</label><br />
						</fieldset>

						<fieldset id="types_affichage">
							<legend>Mode de représentation</legend>
							<input type="checkbox" id="map" name="map" /><label for="map">Carte</label><br />
							<input type="checkbox" id="bar" checked="checked" name="bar" /><label
								for="bar">Diagramme en colonnes</label><br /> <input
								type="checkbox" id="pie" name="pie" /><label for="pie">Diagramme
								à secteurs</label><br /> <input type="checkbox" id="grid"
								name="grid" checked="checked" /><label for="grid">Tableau</label>
						</fieldset>

						<fieldset>
							<legend>Format des données</legend>
							<input id="valeur_absolue" type="radio" name="format"
								checked="checked" /><label for="valeur_absolue">Valeurs absolues</label><br />
							<input id="valeur_relative" type="radio" name="format" /><label
								for="valeur_relative">Valeurs relatives</label><br />
						</fieldset>
					</form>
				</div> <br> <br>
			</td>
			<td>
				<div id="container">

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
	<script src="<?php echo js_url("base");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("init_filtres");?>"
		type="text/javascript"></script>
	<script src="<?php echo js_url("base2");?>" type="text/javascript"></script>
	<!--script type='text/javascript'>
			if ( $.getUrlVar('niveau') ) {
				url='http://www.sigegis.ugb-edu.com/main_controller/afficher?niveau='+$.getUrlVar('niveau')+'&param='+param;
			}
			else {
			url='http://www.sigegis.ugb-edu.com/main_controller/afficher?param='+param;
			}
			$('#list').jqGrid({
				url:url,
				autowidth:true,
				datatype: 'xml',
				mtype: 'GET',
				colNames:['Nom du candidat','Nombre de voix'],
				colModel :[
				{
					name:'nomCandidat', index:'nomCandidat', search:true},
					{
						name:'nbVoix', index:'nbVoix', width:80, align:'nbVoix',sortable:true}
						],
						pager: '#pager',
						rowNum:20,
						rowList:[20,30,50,100],
						sortname: 'nbVoix',
						sortorder: 'desc',
						viewrecords: true,
						gridview: true,
			}).navGrid('#pager',{edit:true,add:true,del:true});</script-->
	<script src="<?php echo js_url("dragAndDrop");?>"
		type="text/javascript"></script>
	<script src="<?php echo js_url("tooltips");?>" type="text/javascript"></script>
	<script src="<?php echo js_url("style");?>" type="text/javascript"></script>
</body>
</html>
