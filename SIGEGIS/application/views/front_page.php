<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<!-- 
   Description : Template SIGeGIS 
   Auteur : Maissa Mbaye
   Email : maissa.mbaye@ugb.edu.sn
   Version : 1.5.3
   Date de dernière modification : 28/11/2012 à 16:07
   Dépendances : 
   			JQuery 1.8+, 
   			JQuery UI 1.9.1+ (custom), 
   			Pluggin, Chosen (JQuery) 0.9
   			Google Web font : Yanone Kaffeesatz (needs to be online to see effect)

 -->
<head><?php echo $head;?></head>
<body>
<div id="container">
	<div id="header">	
	<!--  header PK-->	
		<img title="Système d'Information Géographique Electoral" src="<?php echo img_url("logo.png");?>" style="position : absolute; top : 20px; left : 20px; height : 170px; "/>
		
		<br/>
		<br/>
		<?php $styles="chzn-select";?>
		<?php $filtres=array("sources","elections","tours","pays","regions","departements","collectivites","centres");?>
		<?php $labels_filtres=array("sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>	
		<?php echo $menu;?> 
	</div>
	

	<div id="content">
		
		<h1 id="titre"></h1>
					
		<div  id="bloc_horizontal_filtres"class="ui-widget-content">
			<!-- data-placeholder="Choissisez une source" style="width:180px;"  class="chzn-select"  -->
			<form>
				<?php 
				foreach ($filtres as $filtre)
					echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
				echo "<div style='clear:both;'></div>";
				?>
			</form>
				
		</div> <!-- fin bloc_horizontal_filtres -->		
					
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
			<a id="visualiser" href="<?php echo base_url("visualiser/resultats?type=presidentielle&niveau=globaux");?>" class="boutons" title="Visualiser les résultats des élections passées">Visualiser des résultats</a>
			<a id="analyser" href="<?php echo base_url("analyser");?>" class="boutons" title="Ensemble d'outils permettant d'effectuer des comparaisons">Comparer des résultats</a> 
			</p>
		</div>

		<div id="theGrid">
			<table id="list"></table>
			<div id="pager"></div>
		</div>

		<div id="chartdiv1" class="diagrammes"></div>
		<div id="chartdiv2" class="diagrammes"></div>
		<br />
		
		<div id="export">
		<h4>Exporter</h4>
			<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img src="../../assets/images/print.png" alt="Imprimer toute la page"/></button>
			<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button>
			<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button>
		</div>
		
		<div id="fenetre" style="display:none;"><div id="contenu_modale"></div></div>
		
		</div>			
	
		<?php echo $options_menu;?>
			 			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>