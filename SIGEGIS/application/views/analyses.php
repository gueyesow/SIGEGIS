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
		<?php $filtres=array("sources");?>
		<?php $labels_filtres=array("sources"=>"Source...");?>	
		<?php echo $menu;?> 
	</div>
	

	<div id="content">
		<h1 id="titre"></h1>
					
		<div id="bloc_horizontal_filtres" class="ui-widget-content">		
				<button id="ouvrir" class="boutons" title="Ensemble d'outils permettant d'effectuer des analyses">Faire une nouvelle requête</button>
				<button id="comparer" class="boutons" title="Ensemble d'outils permettant d'effectuer des analyses">Comparer ces résultats à ...</button>
				<button id="simple" class="boutons" title="Annuler la comparaison">Quitter le mode comparaison</button>
				<button id="reset" class="boutons" title="Réinitialiser">Réinitialiser</button>
				<a href="<?php echo site_url("visualiser/exemples");?>" class="boutonhelp">Exemples</a>
		</div> <!-- fin bloc_horizontal_filtres -->


		<div id="dialog_zone_des_options" title="Utilitaire SIGeGIS">
				<div class="zone_des_options_analyse">
				
					<form>
						<?php 
						foreach ($filtres as $filtre)
							echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
						echo "<div style='clear:both;'></div>";
						?>
					</form>
					
					<div id="accordion">
					
					<!-- Premier bloc du menu accordéon -->
					<div>
						<h3><a href="#">Analyser suivant les années</a></h3>
						<div id="accordion_item1">
							<form  style="clear:both;" method="post" action="">															
									<fieldset>			
									<div class="filtres_accordions1" style="width:100%">						
										<?php 
										echo form_dropdown("decoupage_annee","decoupage_annee",$styles,"Découpages");
										
										$options = array(
												'pays'  => 'Pays',
												'region'    => 'Région',
												'departement'   => 'Département',
												'centre' => 'Centre',
										);
										
										echo form_dropdown2("niveauAgregation1","niveauAgregation1",$styles,$options,"Agréger par");
	
										$options=array("premier_tour"=>"Premier tour","second_tour"=>"Second tour");
										echo form_dropdown2("ana_tour","ana_tour",$styles,$options,"Tour");
	
										echo form_dropdown("localite","localite",$styles,"Lieu");
										echo "<div style='clear:both;'></div>";
										?>
									</div>
										
										<table id="swapListAnnees" class="swapList">
											<tr><td colspan="3"><b>Choisir les années</b></td></tr>
											<tr>											
												<td><select id="choixmultipleA" multiple="multiple"></select></td>
												<td>																								
													<input id="MoveRight" type="button" value=" &gt;&gt; " class="move" />
													<input id="MoveLeft" type="button" value=" &lt;&lt; " class="move" /> 
												</td>
												<td>
													<select id="choixmultipleB" multiple="multiple" class="theToolTip" title="Cliquez ici pour réinitialiser la sélection des candidats"></select>
												</td>
											</tr>
										</table>
	
										<b>Choisir les candidats (10 Max.)</b><br>
										<table id="swapListCandidatsAnnees" class="swapList">
											<tr>
												<td>
													<select id="choixCandidatA" multiple="multiple"></select>
												</td>
												<td>												 												
													<input id="MoveRightCandidat" type="button" value=" &gt;&gt; " class="move" />
													<input id="MoveLeftCandidat" type="button" value=" &lt;&lt; " class="move" />
												</td>
												<td>
													<select id="choixCandidatB" multiple="multiple"></select>
												</td>
											</tr>
										</table>
										<input id="validerAnnees" type="button" value="Valider" style="float:right;" />
									</fieldset>
									</form>
									</div>
								</div><!-- Fin premier bloc du menu accordéon -->
								
								<!-- Deuxième bloc du menu accordéon -->
								<div>
									<h3><a href="#">Analyser suivant les localités</a></h3>
									<div id="accordion_item2">
									<form style="clear:both;" method="post" action="">
									<fieldset>
									<div class="filtres_accordions2">
										<?php 
										echo form_dropdown("decoupage_localite","decoupage_localite",$styles,"Découpages");
	
										$options = array(
												'pays'  => 'Pays',
												'region'    => 'Région',
												'departement'   => 'Département',
												'centre' => 'Centre',
										);
										echo form_dropdown2("niveauAgregation2","niveauAgregation2",$styles,$options,"Agréger par");
										?>
										
										<?php
										$filtres=array("elections","tours","pays","regions","departements","collectivites","centres");
										$labels_filtres=array("elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");
										foreach ($filtres as $filtre) {
											echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
										}
										?>
									</div>
	
										<table id="swapListLocality" class="swapList">
											<tr>
												<td colspan="3"><b>Choisir les localités</b></td>
											</tr>
											<tr>
												<td>
													<select id="choixMultipleLocalitesA" multiple="multiple"></select>
												</td>
												<td>												 
													<input id="MoveRightLocalite" type="button" value=" &gt;&gt; " class="move" />
													<input id="MoveLeftLocalite" type="button" value=" &lt;&lt; " class="move" />
												</td>
												<td>
													<select id="choixMultipleLocalitesB" multiple="multiple"></select>
												</td>
											</tr>
										</table>
										
										<table id="swapListCandidatsLocality" class="swapList">
											<tr><td colspan="3"><b>Choisir les candidats (10 Max.)</b></td></tr>
											<tr>
												<td>
													<select id="choixCandidatLocaliteA" multiple="multiple" style="margin: 0;"></select>
												</td>
												<td>												 
													<input id="MoveRightCandidatLocalite" type="button"	value=" &gt;&gt; " class="move" />
													<input id="MoveLeftCandidatLocalite" type="button" value=" &lt;&lt; " class="move" />
												</td>
												<td>
													<select id="choixCandidatLocaliteB" multiple="multiple"></select>
												</td>
											</tr>
										</table>
										<input id="validerLocalites" type="button" value="Valider" style="float:right;"/>
									</fieldset>
									</form>
									</div>
								</div>
								<!-- Fin deuxième bloc du menu accordéon -->
							</div>		
							<!-- Fin du menu accordéon -->											
					</div>							
					
					</div>
					<!-- Fin de la fenetre modale contenant le menu accordéon -->
					
					<div id="help">
						<h1>Etapes à suivre</h1>
						<p>
						Vous êtes dans l'utilitaire SIGEGIS<br />
						Deux options vous sont offertes:<br /><br />
						<b>1) Comparer suivant une localité précise</b><br />
						Avec cette option, vous pouvez sélectionner le lieu de vote qui vous intéresse puis les candidats ciblés<br /><br />
						<b>2) Comparer suivant une élection précise</b><br />
						Les options sont quasi identiques aux précédentes. Il suffit juste de déplacer les éléments qui vous intéressent de la zone à gauche à celle de droite.<br />
						<br /><br />
						Les modes de représentation disponibles se trouvent dans le menu de gauche.
						</p>
						
						<ol type="a">
							<li>Tableau</li>
							<li>Carte</li>
							<li>Courbes</li>
							<li>Diagramme en bâtons</li>
							<li>Diagramme circulaire</li>
						</ol> 
						
						<p> 
						Cliquez sur <b style="font-size:14px;">Faire une nouvelle requête</b> pour débuter. 
						</p>
					</div>
					
					<div id="theGrid1">
					<h1 id="titleGrid1"></h1>
						<table id="list"></table>
						<div id="pager"></div>
					</div>
					
					<div id="theGrid2">
					<h1 id="titleGrid2"></h1>
						<table id="list2"></table>
						<div id="pager2"></div>
					</div>
					
					<div id="chartdiv1" class="diagrammes"></div>
					<div id="chartdiv2" class="diagrammes"></div>
					<div id="chartdiv3" class="diagrammes"></div>
					<div id="chartdiv4" class="diagrammes"></div>
					<br />			
		
	</div>			
	
		<?php echo $options_menu;?>			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>