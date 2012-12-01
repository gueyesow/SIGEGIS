<?php if(!$this->session->userdata('logged_in')) show_error("ACCES NON AUTORISE");?>
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
			<!-- data-placeholder="Choissisez une source..." style="width:180px;"  class="chzn-select"  -->
			<form>
				<?php 
				foreach ($filtres as $filtre)
					echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
				echo "<div style='clear:both;'></div>";
				?>
			</form>
			
				
		</div> <!-- fin bloc_horizontal_filtres -->
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
				    	<input type="radio" id="button_centre" name="radio" />
				        <label for="button_centre">Niveau centre</label>
				        <input type="radio" id="button_departement" name="radio" />
				        <label for="button_departement">Niveau département</label>
				    </div></td></tr></table>
				</form><br /><br />
				<h1>RESULTATS</h1>
				<h3 id="titre"></h3>
				<div id="theGrid">
					<table id="list"></table>
					<div id="pager"></div>
				</div>
				<p>
				<span style="font-weight: bold;color: #0000ff;">Rappel: </span> 
			    <b>Niveau centre</b> signifie que la granularité est centre. Même raisonnement pour <b>Niveau département</b>. Aucune agrégation n'est faite sur les données.
			    </p> 
		</div>			
	
		<?php echo $options_menu;?>			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>