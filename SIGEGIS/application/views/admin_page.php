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
			
		<?php echo $menu;?> 
	</div>
	

	<div id="content">
		<h1 id="titre"></h1>
			<h2>Administration</h2>
			<table id="admin_menu">
			<tr><th>Elections, candidats, listes et uploads</th><th>Localités</th><th>Résultats</th><th>Participation</th></tr>
			<tr>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editSources")?>">Sources</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editElections?type=presidentielle")?>">Elections</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editCandidats")?>">Candidats</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editListes")?>">Partis et coalitions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editUsers")?>">Gestion des droits</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/upload")?>">Gestion des images</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=pays")?>">Pays</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=region")?>">Régions</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=departement")?>">Départements</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=collectivite")?>">Collectivites</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editLocalites?typeLocalite=centre")?>">Centres</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editResultats?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			<td>
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=presidentielle&amp;niveau=cen")?>">Présidentielles</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=legislative&amp;niveau=cen")?>">Législatives</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=regionale&amp;niveau=cen")?>">Régionales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=municipale&amp;niveau=cen")?>">Municipales</a><br />
			<a class="boutonAdmin" href="<?php echo site_url("admin/editParticipations?type=rurale&amp;niveau=cen")?>">Rurales</a><br />
			</td>
			</tr>
			</table>						
		</div>			
	
		<?php echo $options_menu;?>			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>