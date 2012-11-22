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
			
				<div id="winners"></div>				
			</td>
			<td id="content">			
				<div id="senmaps"></div>
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
