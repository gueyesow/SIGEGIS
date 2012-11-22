<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
</head>

<body>
<div id="wrapper">
	<?php $styles="";?>
	<?php $filtres=array("sources","elections","tours","pays","regions","departements","collectivites","centres");?>
	<?php $labels_filtres=array("sources"=>"Source","elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays");?>	

	<?php echo $menu;?>		

		<div id="zone_des_filtres">
		<form>
			<?php 
			foreach ($filtres as $filtre)
				echo form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
			echo "<div style='clear:both;'></div>";
			?>
		</form>
	</div>
	<br />
	<br />

	<table id="wrapper-table">
		<tr>
			<td id="left-sidebar">
			
			<?php echo $options_menu;?>
				
				<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img height="58px" src="../../assets/images/print.png" alt="Imprimer toute la page"/></button>
				<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img height="58px" src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button>
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img height="58px" src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button>
			</td>
			<td id="content">
				<div>
					<h1 id="titre"></h1>
					
						<h1>Connexion</h1>
					   <p id="login_errors"><?php echo validation_errors(); ?></p>
					   <?php echo form_open('admin/verifylogin',array("id"=>"connection_form")); ?>
					   <table><tr><td>
					     <label for="username">Identifiant:</label></td><td>
					     <input type="text" size="20" id="username" name="username" value="<?php echo set_value('username'); ?>"/></td></tr><tr><td>
					     <label for="password">Mot de passe:</label></td><td>
					     <input type="password" size="20" id="password" name="password"/></td></tr>
					     <tr><td colspan="2">					     
					     <input type="submit" value="Login"/></td></tr></table>
					   </form>

					<div style='clear: both;'></div>
				</div>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript">
		$("#left-sidebar input, #left-sidebar button, select").attr("disabled","disabled");
		$("#menu li:gt(0)").hide();
	</script>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
   