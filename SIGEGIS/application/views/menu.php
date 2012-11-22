<?php $typeElection=empty($_GET["type"])?"presidentielle":$_GET["type"];?>
<?php $basename = basename(current_url());?>
<div id="menu">
	<ul>
		<li id="menu_front"><a class="actif" href="<?php echo site_url();?>">Accueil</a></li>			
		<li id="menu_globaux"><a href="<?php echo site_url("visualiser/$basename?type=".$typeElection."&amp;niveau=globaux");?>">Résultats globaux</a></li>
		<li id="menu_pays"><a href="<?php echo site_url("visualiser/$basename?type=".$typeElection."&amp;niveau=pays");?>">Résultats par pays</a></li>
		<li id="menu_reg"><a href="<?php echo site_url("visualiser/$basename?type=".$typeElection."&amp;niveau=reg");?>">Résultats régionaux</a></li>
		<li id="menu_dep"><a href="<?php echo site_url("visualiser/$basename?type=".$typeElection."&amp;niveau=dep");?>">Résultats départementaux</a></li>
		<li id="menu_cen"><a href="<?php echo site_url("visualiser/$basename?type=".$typeElection."&amp;niveau=cen");?>">Résultats par centre</a></li>
		<li id="menu_stats"><a href="<?php echo site_url("analyser/participation?type=".$typeElection."&amp;niveau=globaux");?>">Statistiques</a></li>
		<!--li id="menu_maps"><a href="http://www.sigegis.ugb-edu.com/visualiser/map?type=presidentielle&niveau=dep&year=2012">Maps</a></li-->
		<li id="menu_exemples"><a href="<?php echo site_url("visualiser/exemples");?>">Exemples</a></li>
		<li id="menu_apropos"><a href="<?php echo site_url("visualiser/apropos");?>">A propos</a></li>
		<?php if($this->session->userdata('logged_in')) {?>
		<li id="menu_admin"><a href="<?php echo site_url("admin");?>">Administration</a></li>
		<li id="menu_decon"><a class="actif" href="<?php echo site_url("admin/logout");?>">Déconnexion</a></li>
		<?php }?>
	</ul>
</div>