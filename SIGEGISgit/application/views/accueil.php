<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIGEGIS</title>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui.jqgrid"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("front_page"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui-lightness/jquery-ui-1.8.21.custom"); ?>" /> 
<script src="<?php echo js_url("jqgrid/js/jquery-1.7.2.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/plugins/jquery.searchFilter");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-en");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>" type="text/javascript"></script>
<!--script src="<?php echo js_url("jquery-ui/js/jquery-1.7.2.min");?>" type="text/javascript"></script-->
<script src="<?php echo js_url("jquery-ui/js/jquery-ui-1.8.21.custom.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>
</head> 

<body>
<div id="wrapper">
<div id="header-wrap">
<ul>
	<li><a href="#main">Accueil</a></li>
	<li><a href="#services">Aide</a></li>
	<li><a href="#portfolio">A propos de...</a></li>                
</ul>
</div>

<div id="content">
<table id="boutons">
	<tr>
		<td class="bg"><div style="margin:auto;width: 98%;height:400px;margin-bottom: :100px;" ></div></td>
		<td>
		<div id="corps"  class="ui-corner-all">
		<h1>Bienvenue</h1>
		<table cellpadding="10">
			<tr>
				<td><div class="bouton" id="button1"></div></td>
				<td><div class="bouton" id="button2"></div></td>
				<td><div class="bouton" id="button3"></div></td>
			</tr>
			<tr>
				<td><div class="bouton" id="button4"></div></td>
				<td><div class="bouton" id="button5"></div></td>
				<td><div class="bouton" id="button6"></div></td>
			</tr>
		</table>
		
		<div><p></p></div>
		<div class="ui-widget">
		<form action="">
			<table>
				<tr><td><label>Identifiant</label></td><td><input class="ui-state-default ui-corner-all" type="text" name="" /></td></tr>
				<tr><td><label>Mot de passe</label></td><td><input class="ui-state-default ui-corner-all" type="password" name="" /></td></tr>
			</table>
		</form>
		</div>
		</div>
		</td>
	</tr>
</table>

</div>
<div id="footer"><p>&copy; Copyright SIGEGIS | Université Gaston Berger de Saint-Louis du Sénégal | 2012</p></div>
</div>
<script type="text/javascript">
$("#button1").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type=presidentielle&niveau=globaux";} );
$("#button2").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type=legislative&niveau=globaux";} );
$("#button3").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/visualiser?type=locale&niveau=globaux";} );
$("#button4").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type=presidentielle";} );
$("#button5").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type=legislative";} );
$("#button6").on("click", function(){window.location="http://www.sigegis.ugb-edu.com/main_controller/analyser?type=locale";} );
</script>
</body>
</html>