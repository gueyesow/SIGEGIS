<div id="menu-css">
		<ul>
			<li><a class="actif" href="<?php echo site_url();?>">Accueil</a></li>
			<li><a href="<?php echo site_url("main_controller/administration");?>">Administration</a></li>
			<li><a href="<?php echo site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=globaux");?>">Résultats globaux</a></li>
			<li><a href="<?php echo site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=reg");?>">Résultats régionaux</a></li>
			<li><a href="<?php echo site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=dep");?>">Résultats départementaux</a></li>
			<li><a href="<?php echo site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=cen");?>">Résultats au niveau des centres</a></li>
			<li><a href="<?php echo site_url("main_controller/participation?type=".$typeElection);?>">Statistiques</a></li>
		</ul>
</div>