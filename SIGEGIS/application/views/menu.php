<?php $typeElection=empty($_GET["type"])?"presidentielle":$_GET["type"];?>
<?php $basename = basename(current_url());?>
<div id="menu">
	<ul>
		<li><a class="actif" href="<?php echo site_url();?>">Accueil</a></li>			
		<li><a href="<?php echo site_url("main_controller/$basename?type=".$typeElection."&amp;niveau=globaux");?>">Résultats globaux</a></li>
		<li><a href="<?php echo site_url("main_controller/$basename?type=".$typeElection."&amp;niveau=reg");?>">Résultats régionaux</a></li>
		<li><a href="<?php echo site_url("main_controller/$basename?type=".$typeElection."&amp;niveau=dep");?>">Résultats départementaux</a></li>
		<li><a href="<?php echo site_url("main_controller/$basename?type=".$typeElection."&amp;niveau=cen");?>">Résultats par centre</a></li>
		<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection."&amp;niveau=globaux");?>">Statistiques</a></li>
		<li><a href="<?php echo site_url("main_controller/exemples");?>">Exemples</a></li>
		<li><a href="<?php echo site_url("main_controller/apropos");?>">A propos</a></li>
	</ul>
</div>