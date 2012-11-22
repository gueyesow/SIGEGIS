<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("sources");?>
	<?php $labels_filtres=array("sources"=>"Source");?>

	<?php echo $menu;?>
	
	<br />
	<br />

	<table id="wrapper-table">
		<tr>
			<td id="left-sidebar">
			
				<?php echo $options_menu;?>
				
				<button id="ouvrir" class="theToolTip boutons" title="Ensemble d'outils permettant d'effectuer des analyses">Faire une nouvelle requête</button>
				<button id="comparer" class="theToolTip boutons" title="Ensemble d'outils permettant d'effectuer des analyses">Comparer ces résultats à ...</button><br />
				<button id="reset" class="theToolTip boutons" title="Ensemble d'outils permettant d'effectuer des analyses">Réinitialiser</button><br />
				
				
				<div id="dialog_zone_des_options"  title="Utilitaire SIGEGIS">
				<div class="zone_des_options_analyse">
				<form id="drag">
					<?php 
					foreach ($filtres as $filtre)
						echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
					echo "<div style='clear:both;'></div>";
					?>
				</form>
					<div id="accordion">
					<div>
					<h3><a href="#">Analyser suivant la localité</a></h3>
					<div id="accordion_item1">
						<form  style="clear:both;" method="post" action="">															
								<fieldset>									
									<?php 
									echo form_dropdown("ana_decoupage","ana_decoupage",null,"Découpages");
									
									$options = array(
											'pays'  => 'Pays',
											'region'    => 'Région',
											'departement'   => 'Département',
											'centre' => 'Centre',
									);
									
									echo form_dropdown2("niveauAgregation1","niveauAgregation1",null,$options,"Agréger par");

									$options=array("premier_tour"=>"Premier tour","second_tour"=>"Second tour");
									echo form_dropdown2("ana_tour","ana_tour",null,$options,"Tour");

									echo form_dropdown("localite","localite",null,"Lieu");
									?>
									
									<table class="swapList">
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
									<table class="swapList">
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
									<input id="valider" type="button" value="Valider" style="float:right;" />
								</fieldset>
								</form>
								</div>
								</div>
								<div>
								<h3><a href="#">Analyser suivant l'année</a></h3>
								<div id="accordion_item2">
								<form style="clear:both;" method="post" action="">
								<fieldset>
									<?php 
									echo form_dropdown("ana_decoupage_localite","ana_decoupage_localite",null,"Découpages");

									$options = array(
											'pays'  => 'Pays',
											'region'    => 'Région',
											'departement'   => 'Département',
											'centre' => 'Centre',
									);
									echo form_dropdown2("niveauAgregation2","niveauAgregation2",null,$options,"Type de localité");
									?>
									
									<?php
									$filtres=array("elections","tours","pays","regions","departements","collectivites","centres");
									$labels_filtres=array("elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");
									foreach ($filtres as $filtre) {
										echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
									}
									?>

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
									
									<table class="swapList">
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
									<input id="validerLocalite" type="button" value="Valider" style="float:right;"/>
								</fieldset>
								</form>
								</div>
								</div>
							</div>													
					</div>		
					</div>		
			</td>

			<td id="content">
				<div>								
									
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
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	</div>
	
	<?php echo $scripts;?>
	
</body>
</html>
