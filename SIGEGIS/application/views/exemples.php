<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
</head>

<body>
<div id="wrapper">
	
	<?php echo $menu;?>		

	<table id="wrapper-table">
		<tr>
			<td id="left-sidebar" style="width:200px">
				<div id="help">
					<h1>Bienvenue</h1>					
					<p>
						<a class="exemple" id="visualiser" href="#">Visualiser les résultats d'une élection</a>
						<a class="exemple" id="analyser" href="#">Analyser les résultats d'une élection</a>							
					</p>					
				</div>
			
			</td>
			<td id="content">
				<div>
					<h1 id="titre"></h1>
					<?php $exemples=array("visualiser","analyser");?>					
					<div id="fenetre">
					<?php foreach ($exemples as $exemple){?>
					<div id="<?php echo "bloc_".$exemple;?>">
						<OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" WIDTH="100%" HEIGHT=650 CODEBASE="http://active.macromedia.com/flash5/cabs/swflash.cab#version=7,0,0,0">
						<PARAM NAME=movie VALUE="http://www.sigegis.ugb-edu.com/assets/screencasts/<?php echo $exemple;?>.swf">
						<PARAM NAME=play VALUE=true>
						<PARAM NAME=loop VALUE=false>
						<PARAM NAME=wmode VALUE=transparent>
						<PARAM NAME=quality VALUE=low>
						<EMBED SRC="http://www.sigegis.ugb-edu.com/assets/screencasts/<?php echo $exemple;?>.swf" WIDTH="100%" HEIGHT=650 quality=low loop=false wmode=transparent TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
						</EMBED>
						</OBJECT>
						</div>
					<?php }?>						
					</div>					

					<div style='clear: both;'></div>
				</div>
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 

<!--  Fermeture Wrapper -->
				
	<?php echo $scripts;?>
		
</body>
</html>
