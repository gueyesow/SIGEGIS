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
				    <div id="radio">
				        <input type="radio" id="allCandidats" name="radio" /><label for="allCandidats">Tous les candidats</label>
				        <input type="radio" id="notAllCandidats" name="radio" checked="checked" /><label for="notAllCandidats">Filtrer les candidats</label>
				    </div>
				</form>					
				<br /><br />
			
				<div id="theGrid">
					<table id="list"></table>
					<div id="pager"></div>
				</div> 
		</div>			
	
		<?php echo $options_menu;?>			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>