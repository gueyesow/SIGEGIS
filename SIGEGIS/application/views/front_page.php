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
				<div>
					<h1 id="titre"></h1>
					
					<div id="help">
						<h1>Bienvenue</h1>
						<p>
						Cette plateforme est simple d'utilisation et vous permet de visualiser les résultats des élections passées ainsi que d'effectuer des analyses sur les données électorales.<br />												
					
						La navigation entre les différentes élections se fait grâce au menu de gauche et la liste <b>Année</b> en haut <img height="50px" alt="screenshot" src="../../assets/images/capture.jpg">.<br />
						De même, les changements de mode de représentation des données se feront à partir de ce menu:
						</p>
						<ol type="a">
							<li>Tableau</li>
							<li>Carte</li>
							<li>Diagramme circulaire</li>
							<li>Diagramme en bâtons</li>
						</ol> 
						
						<p> 
						Chosissez l'opération que vous souhaitez effectuer:
						<button id="visualiser" class="boutons" title="Visualiser les résultats des élections passées">Visualiser des résultats</button>
						<button id="analyser" class="boutons" title="Ensemble d'outils permettant d'effectuer des comparaisons">Comparer des résultats</button> 
						</p>
					</div>

					<div id="theGrid">
						<table id="list"></table>
						<div id="pager"></div>
					</div>

					<div id="chartdiv1" class="diagrammes"></div>
					<div id="chartdiv2" class="diagrammes"></div>
					<br />


<div id="fenetre" style="display:none;">
<div id="contenu_modale"></div>

</div>					

					<div style='clear: both;'></div>
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
