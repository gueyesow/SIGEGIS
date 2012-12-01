<?php $typeElection=empty($_GET["type"])?"presidentielle":$_GET["type"];?>

<div id="menu">
	<ul id="menu_top">
		<!-- Pour ajouter un element sur la liste il suffit d'ajouter un li a -->	
		<!-- Pour ajouter un menu déroulant il suffit d'ajouter un li > ul > li > a -->
		
		<?php if($this->session->userdata('logged_in')) {?>		
		<li id="menu_admin">
		<a href="<?php echo site_url("admin");?>">Administration</a>
		<ul>
		<li id="menu_decon"><a class="actif" href="<?php echo site_url("admin/logout");?>">Déconnexion</a></li>
		</ul>
		</li>
		<?php }?>
		
		<li id="menu_apropos"><a href="<?php echo site_url("visualiser/credits");?>">Crédits</a></li>
			
		<li id="menu_resultats">
			<a href="<?php echo site_url("visualiser?type=".$typeElection."&amp;niveau=globaux");?>">Résultats éléctions</a>
			<ul >
			  <li id="menu_globaux"><a href="<?php echo site_url("visualiser?type=".$typeElection."&amp;niveau=globaux");?>">Globaux</a></li>
	          <li id="menu_reg"><a href="<?php echo site_url("visualiser?type=".$typeElection."&amp;niveau=reg");?>">Par région</a></li>
	          <li id="menu_dep"><a href="<?php echo site_url("visualiser?type=".$typeElection."&amp;niveau=dep");?>">Par département</a></li>
	          <!-- li ><a href="#">Par collectivité</a></li-->
	          <li id="menu_cen"><a href="<?php echo site_url("visualiser?type=".$typeElection."&amp;niveau=cen");?>">Par Centre de vote</a></li>
	        </ul>
		</li>
		<li>
			<a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=globaux");?>">Statistiques</a>
			<ul >
			  <li id="menu_globaux"><a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=globaux");?>">Niveau national</a></li>
	          <li id="menu_reg"><a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=reg");?>">Par région</a></li>
	          <li id="menu_dep"><a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=dep");?>">Par département</a></li>
	          <li id="menu_cen"><a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=cen");?>">Par Centre de vote</a></li>
	        </ul>			
		</li>
		<li id="menu_analyse">
			<a href="<?php echo site_url("analyser");?>">Analyse des élections</a>
			<!--ul >
	          <li ><a href="#">Dimension Candidat</a></li>
	          <li ><a href="#">Dimension Territoire</a></li>
	          <li ><a href="#">Dimension Election</a></li>
	        </ul-->
		</li>
		<!-- Ajouter la classe selected pour indiquer à quel niveau se trouve l'utilisateur -->	
		<li id="menu_front"><a style="border-left: 1px solid #917C6F;" href="<?php echo site_url();?>" class="selected"> Accueil </a></li>
			</ul>
    	</div>   