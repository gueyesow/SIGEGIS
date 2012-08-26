<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIGEGIS &gt; Analyses</title>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui.jqgrid"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("analyse"); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui-lightness/jquery-ui-1.8.21.custom"); ?>" /> 
<script src="<?php echo js_url("jqgrid/js/jquery-1.7.2.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/plugins/jquery.searchFilter");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-fr");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jquery-ui/js/jquery-ui-1.8.21.custom.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>
</head> 

<body>

 <?php $styles="";?>
 <?php $filtres=array("sources");?>
 <?php $labels_filtres=array("sources"=>"Source");?>
 
 <div id="menu-css">
<ul>
	<li><a class="actif" href="<?php echo site_url();?>" >Accueil</a></li>
	<li><a href="<?php echo site_url("main_controller/administration");?>" >Administration</a></li>
	<li><a href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]);?>" >Résultats globaux</a></li>
	<li><a href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=reg");?>">Résultats régionaux</a></li>
	<li><a href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=dep");?>">Résultats départementaux</a></li>
	<li><a href="<?php echo site_url("main_controller/visualiser?type=".$_GET["type"]."&amp;niveau=cen");?>">Résultats au niveau des centres</a></li>                                
</ul>
</div>
 
<div id="zone_des_filtres"> 
  <form id="drag">
	<?php 
		foreach ($filtres as $filtre) 
			echo $this->mon_filtre->form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
		echo "<div style='clear:both;'></div>";
	?>
</form>
</div><br /><br />

<table id="tableau">
	<tr><td id="left-sidebar">
	
	<div class="zone_des_options">
		<form action="">
		<fieldset id="types_elections">
		<legend>Type d'élection à représenter</legend>
		<input id="presidentielle"  type="radio" name="radio" /><label for="presidentielle">Election présidentielle</label><br />
		<input id="legislative"  type="radio" name="radio" /><label for="legislative">Election législative</label><br />
		<input id="locale"  type="radio" name="radio2" /><label for="locale">Election locale</label><br />	
		</fieldset>
		
		<fieldset id="types_affichage">
		<legend>Mode de représentation</legend>
		<input type="checkbox" id="map" name="map" /><label for="map">Carte</label><br />
		<input type="checkbox" id="bar" checked="checked" name="bar" /><label for="bar">Diagramme en colonnes</label><br />
		<input type="checkbox" id="pie" name="pie" /><label for="pie">Diagramme à secteurs</label><br />
		<input type="checkbox" id="grid" name="grid" checked="checked" /><label for="grid">Tableau</label>
		</fieldset>
		
		<fieldset>
		<legend>Format des données</legend>
		<input id="valeur_absolue" type="radio" name="format" checked="checked" /><label for="valeur_absolue">Valeurs absolues</label><br />
		<input id="valeur_relative" type="radio" name="format" /><label for="valeur_relative">Valeurs relatives</label><br />	
		</fieldset>
	</form>
	</div><br><br>
	
	<div class="zone_des_options">	
		<div id="accordion">
		<form method="post" action="">
			<!--div>
				<h3><a href="#">Analyser suivant la localité</a></h3>			
				<fieldset>
				<b>Choisir la localité</b><br>
				<?php 
				//$options=array("tout"=>"Tous les découpages","decoupage2000"=>"Découpage avant 2008","decoupage2008"=>"Découpage après 2008");
				//echo $this->mon_filtre->form_dropdown2("ana_decoupage","ana_decoupage",null,$options,"Découpage")."<br />";
				echo $this->mon_filtre->form_dropdown("ana_decoupage","ana_decoupage",null,"Découpages")."<br />";
				echo "<div style='clear:both;'></div>";
				$options = array(
	                  'pays'  => 'Pays',
	                  'region'    => 'Région',
	                  'departement'   => 'Département',
	                  'centre' => 'Centre',
	            );
	
				echo form_dropdown('ana_localite', $options, 'region')."<br />";
				
				if($_GET["type"]=="presidentielle"){
					$options=array("premier_tour"=>"Premier tour","second_tour"=>"Second tour");
					echo $this->mon_filtre->form_dropdown2("ana_tour","ana_tour",null,$options,"Tour")."<br />";
					echo "<div style='clear:both;'></div>";
				}
				
				echo $this->mon_filtre->form_dropdown("localite","localite",null,"Lieu")."<br />";
				?>
					
				<b>Choisir les années</b><br>
				<table class="swapList">
				<tr>
				<td>
				<select id="choixmultipleA" multiple="multiple" style="margin: 0;">			
				</select>
				</td>
				<td>
				<input id="MoveRight" type="button" value=" >> " class="move" />
				<input id="MoveLeft" type="button" value=" << " class="move"/>
				</td>
				<td>
				<select id="choixmultipleB"  multiple="multiple" style="float: right;margin: 0;"></select>
				</td></tr>
				</table>
				
				<b>Choisir les candidats (10 Max.)</b><br>
				<table class="swapList">
				<tr>
				<td>
				<select id="choixCandidatA" multiple="multiple" style="margin: 0;"></select>
				</td>
				<td>
				<input id="MoveRightCandidat" type="button" value=" &gt;&gt; " class="move" />
				<input id="MoveLeftCandidat" type="button" value=" &lt;&lt; " class="move"/>
				</td>
				<td>
				
				<select id="choixCandidatB"  multiple="multiple" style="float: right;margin: 0;"></select>
				
				</td></tr>
				</table>						
				<input id="valider" type="button" value="Valider" style="float: right;"/>							
				</fieldset>				
			</div-->
			<div>
				<h3><a href="#">Analyser suivant l'année</a></h3>
				<fieldset>
				<?php 
				//$options=array("tout"=>"Tous les découpages","decoupage2000"=>"Découpage avant 2008","decoupage2008"=>"Découpage après 2008");
				//echo $this->mon_filtre->form_dropdown2("ana_decoupage","ana_decoupage",null,$options,"Découpage")."<br />";
				echo $this->mon_filtre->form_dropdown("ana_decoupage_localite","ana_decoupage_localite",null,"Découpages")."<br />";
				echo "<div style='clear:both;'></div>";								
				
				
				$options = array(
						'pays'  => 'Pays',
						'region'    => 'Région',
						'departement'   => 'Département',
						'centre' => 'Centre',
				);
				echo "<b>Choisir le type de localité</b><br>".form_dropdown('ana_localite2', $options, 'region')."<br />";
				echo "<div style='clear:both;'></div>";
	?>
	<div style="clear: both;"></div>
	<?php
		$styles="";
		$filtres=array("elections","tours","pays","regions","departements","collectivites","centres");
 		$labels_filtres=array("elections"=>"Année","tours"=>"Tour","centres"=>"Centre","collectivites"=>"Collectivité","departements"=>"Département","regions"=>"Région","pays"=>"Pays"); 
		foreach ($filtres as $filtre) {
			echo $this->mon_filtre->form_dropdown("$filtre","$filtre",$styles,"$labels_filtres[$filtre]");
		echo "<div style='clear:both;'></div>";
		}
	?>
											
				<table id="swapListLocality" class="swapList">
				<tr>
				<td colspan="3"><b>Choisir les localités</b></td></tr>
				<tr>
				<td>
				<select id="choixMultipleLocalitesA" multiple="multiple" style="margin: 0;">	</select>
				</td>
				<td>				
				<input id="MoveLeftLocalite" type="button" value=" << " class="move"/>
				<input id="MoveRightLocalite" type="button" value=" >> " class="move" />
				</td>
				<td>
				<select id="choixMultipleLocalitesB"  multiple="multiple" style="float: right;margin: 0;"></select>
				</td></tr>
				</table>
				
				<b>Choisir les candidats (10 Max.)</b><br>
				<table class="swapList">
				<tr>
				<td>
				<select id="choixCandidatLocaliteA" multiple="multiple" style="margin: 0;"></select>
				</td>
				<td>				
				<input id="MoveLeftCandidatLocalite" type="button" value=" &lt;&lt; " class="move"/>
				<input id="MoveRightCandidatLocalite" type="button" value=" &gt;&gt; " class="move" />
				</td>
				<td>
				
				<select id="choixCandidatLocaliteB"  multiple="multiple" style="float: right;margin: 0;"></select>
				
				</td></tr>
				</table>	
				<input id="validerLocalite" type="button" value="Valider" style="float: right;"/>				
				</fieldset>					
			</div>	<br />
			</form>		
		</div>	
	</div>
	</td>
	
	<td>
	<div id="container">  
	
	<h1 id="titre"></h1>
	
	<div id="theGrid">
		<table id="list"></table> 
		<div id="pager"></div>
	</div>
	
	<div id="chartdiv1" class="diagrammes"></div>
	<div id="chartdiv2" class="diagrammes"></div>
	<br />
	
	<div style='clear:both;'></div>
	
	</div></td></tr>
</table>
<script src="<?php echo js_url("base");?>" type="text/javascript"></script>
<script src="<?php echo js_url("init_filtres");?>" type="text/javascript"></script>
<script type="text/javascript">
$.ajax({            
	url: 'http://www.sigegis.ugb-edu.com/main_controller/getDecoupages',            			         			   
	dataType: 'json',      
	success: function(json) {
		$("#ana_decoupage,#ana_decoupage_localite").empty();
		$.each(json, function(index, value) {         
			$("#ana_decoupage,#ana_decoupage_localite").append('<option value="'+ index +'">'+ value +'</option>');							
		});
		$("#ana_decoupage option:last,#ana_decoupage_localite option:last").attr("selected","selected");
		$("select[name*=ana_localite]").change();		
		$("select[name*=ana_decoupage_localite]").change();
		//$("select[name*=ana_localite2]").change();
		Annees();	      					
	}           
});

$("select[name*=ana_decoupage]").on("change",function()
{
	//$("select[name*=ana_localite]").change();		
	$("#choixmultipleA,#choixmultipleB,#choixCandidatA,#choixCandidatB").empty();
	Annees();
	$("select[name*=ana_localite]").change();		
});

$("select[name*=ana_decoupage_localite]").on("change",function()
{
	
	$.ajax({            // DATES 
		url: "http://www.sigegis.ugb-edu.com/main_controller/getDatesElections?typeElection="+$.getUrlVar("type")+"&anneeDecoupage="+$("#ana_decoupage_localite").val(),           			         			      
		dataType: 'json',      
		success: function(json) {
			$elections.empty();
			$.each(json, function(index, value) {         
				$elections.append('<option value="'+ index +'">'+ value +'</option>');
				$tours.change();
			});			
					
			$("#choixMultipleLocalitesA,#choixMultipleLocalitesB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
			$("select[name*=ana_localite2]").change();
		}       
	});			
});

$("select[name*=ana_localite]").on("change",function()
{
	
	if ($(this).val()==="pays") { methode="getPays";parametres_analyse+="&niveau=pays";}
	else if ($(this).val()==="region") { methode="getRegions";parametres_analyse+="&niveau=reg";}
	else if ($(this).val()==="departement") { methode="getDepartements";parametres_analyse+="&niveau=dep";}
	else if ($(this).val()==="centre") { methode="getCentres";parametres_analyse+="&niveau=cen";}
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/'+methode+"?typeElection="+$.getUrlVar("type")+"&anneeDecoupage="+$("#ana_decoupage").val();

	$.ajax({        							
		url: $url,    
		dataType: 'json', 
		success: function(json) {			
			$("#localite").empty();
			$.each(json, function(index, value) {         
				$("#localite").append('<option value="'+ index +'">'+ value +'</option>');     
			});										
		}    
	});			
});

$("select[name*=ana_localite2]").on("change",function()
{			
	if ($(this).val()==="pays") { showLocality(); $("#swapListLocality").hide();$("#filtreregions").hide();	$("#filtredepartements").hide();	$("#filtrecollectivites").hide();	$("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()==="region") { showLocality();$("#filtreregions").hide();$("#filtredepartements").hide();	$("#filtrecollectivites").hide();	$("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()==="departement") { showLocality();$("#filtredepartements").hide();$("#filtrecollectivites").hide(); $("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()==="centre") { showLocality(); $("#filtrecentres").hide();$pays.change();}
});

function showLocality(){
	$("#swapListLocality").show();
	$("#filtreregions").show();	
	$("#filtredepartements").show();	
	$("#filtrecollectivites").show();	
	$("#filtrecentres").show();	
}

function Annees()
{
	if ($("select[name*=ana_localite]").val()==="pays") { methode="getPays";}
	if ($("select[name*=ana_localite]").val()==="region") { methode="getRegions";}
	if ($("select[name*=ana_localite]").val()==="departement") { methode="getDepartements";}
	if ($("select[name*=ana_localite]").val()==="centre") { methode="getCentres";}
	$.ajax({            // DATES 
		url: "http://www.sigegis.ugb-edu.com/main_controller/getDatesElections?typeElection="+$.getUrlVar("type")+"&anneeDecoupage="+$("#ana_decoupage").val(),           			         			      
		dataType: 'json',      
		success: function(json) {
			$("#choixmultipleA").empty();
			$.each(json, function(index, value) {         
				$("#choixmultipleA").append('<option value="'+ index +'">'+ value +'</option>');
			});			
		}       
	});
}

$("#choixmultipleB").on("change",function()
{
		$("#choixCandidatA,#choixCandidatB").empty();
			param="";annees="";i=0;
			param+=$sources.val()+","+$("#ana_tour").val()+","+$("#localite").val();
			
			$("#choixmultipleB").children().each(function(){
				if(annees=="") annees+=$(this).text(); else annees+=","+$(this).val();
			});
			
			parametres_analyse="param="+param+"&annees="+annees;

			if ($("select[name*=ana_localite]").val()==="pays") { parametres_analyse+="&niveau=pays"; }
			if ($("select[name*=ana_localite]").val()==="region") { parametres_analyse+="&niveau=reg";}
			if ($("select[name*=ana_localite]").val()==="departement") {parametres_analyse+="&niveau=dep";}
			if ($("select[name*=ana_localite]").val()==="centre") { parametres_analyse+="&niveau=cen";}		
			
			$.ajax({        							    
				url: "http://www.sigegis.ugb-edu.com/main_controller/getCandidatsAnnee",
				data:parametres_analyse,
				dataType:'json',        					     
				success: function(json) {
					annees="";				
					$.each(json, function(index, value) {						
						$("#choixCandidatA").append('<option value="'+ index +'">'+ value +'</option>');						     
					});																				         
				}    
			});
});

/**
 * ON DOIT PRENDRE EN COMPTE LES LOCALITES SELECTIONNEES POUR PREVOIR LE CAS D'UNE ELECTION LOCALE OU LEGISLATIVE
 */
$("#choixMultipleLocalitesB").on("change",function()
{
		$("#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
		param="";localites="";
		param+=$sources.val()+","+$tours.val()+","+$elections.val()+","+$.getUrlVar("type");
		
		$("#choixMultipleLocalitesB").children().each(function(){
			if(localites=="") localites+=$(this).val(); else localites+=","+$(this).val();
		});
		
		parametres_analyse="param="+param+"&localites="+localites;

		if ($("select[name*=ana_localite2]").val()==="pays") { parametres_analyse+="&niveau=pays"; }
		if ($("select[name*=ana_localite2]").val()==="region") { parametres_analyse+="&niveau=reg";}
		if ($("select[name*=ana_localite2]").val()==="departement") {parametres_analyse+="&niveau=dep";}
		if ($("select[name*=ana_localite2]").val()==="centre") { parametres_analyse+="&niveau=cen";}		
		
		$.ajax({        							    
			url: "http://www.sigegis.ugb-edu.com/main_controller/getCandidatsLocalite",
			data:parametres_analyse,
			dataType:'json',        					     
			success: function(json) {
				annees="";				
				$.each(json, function(index, value) {						
					$("#choixCandidatLocaliteA").append('<option value="'+ index +'">'+ value +'</option>');						     
				});																				         
			}    
		});
});
$("#ana_tour").on("change",function()
{
	$("#choixCandidatA,#choixCandidatB").empty();
	$("#choixmultipleB").change();
});
$tours.on("change",function()
		{
			$("#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
			$("#choixMultipleLocalitesB").change();
		});

</script>
<script src="<?php echo js_url("analyse");?>" type="text/javascript"></script>
<script src="<?php echo js_url("dragAndDrop");?>" type="text/javascript"></script>
<script src="<?php echo js_url("style");?>" type="text/javascript"></script>
</body>
</html>