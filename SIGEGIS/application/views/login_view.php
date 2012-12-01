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
<head><?php echo $head;?><style type="text/css">#container #content{min-height: 700px;}</style></head>
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
	
	<h1>Connexion</h1>
	<p id="login_errors"><?php echo validation_errors(); ?></p>
	<?php echo form_open('admin/verifylogin',array("id"=>"connection_form")); ?>
		<table>
			<tr><td>
			<label for="username">Identifiant:</label></td><td>
		    <input type="text" size="20" id="username" name="username" value="<?php echo set_value('username'); ?>"/></td></tr><tr><td>
		    <label for="password">Mot de passe:</label></td><td>
		    <input type="password" size="20" id="password" name="password"/></td></tr>
		    <tr><td colspan="2">					     
		    <input type="submit" value="Connexion"/>
		    </td></tr>
	   	</table>
   	</form>
   	</div>
	
		<?php echo $options_menu;?>			
		<?php echo $footer;?>
</div> <!-- Fin de content  -->

<!--panel de choix des -->


<?php echo $scripts;?>
</body>
</html>