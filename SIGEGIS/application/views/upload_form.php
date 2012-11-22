<?php if(!$this->session->userdata('logged_in')) show_error("ACCES NON AUTORISE");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php echo $head;?>
<style type="text/css">td{vertical-align: top;} table{width: 100%;} #content button{background: url("../../assets/images/cross.png") no-repeat;border:none;} li{list-style: none;} #retour{height:450px; overflow: scroll;}</style>
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
				echo form_dropdown($filtre,$filtre,$styles,$labels_filtres[$filtre]);
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
				
				<button id="imprimer" class="theToolTip" title="Imprimer toute la page"><img src="../../assets/images/print.png" alt="Imprimer toute la page"/></button>
				<button id="pdf" class="theToolTip" title="Exporter les graphiques au format PDF"><img src="../../assets/images/pdf.png" alt="Exporter au format PDF"/></button>
				<button id="csv" class="theToolTip" title="Exporter les données au format CSV"><img src="../../assets/images/csv.png" alt="Exporter au format CSV"/></button>
			</td>
			<td id="content">
		<?php echo $error;?>

<table>
<tr>
<td>
<h2>Uploader les images des</h2>
<?php echo form_open_multipart('admin/do_upload',array('id' => 'formulaire'));?>
<label for="candidats">Candidats</label><input type="radio" name="repertoire" value="candidats" id="candidats" checked="checked"/>
<label for="partis">Partis</label><input type="radio" name="repertoire" value="partis" id="partis"/>
<label for="snmaps">Cartes du Sénégal</label><input type="radio" name="repertoire" value="snmaps" id="snmaps"/>
<br />

<input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="Upload" />
 <p>
    <span style="font-weight: bold;color: #0000ff;">Rappel: </span> 
    Sélectionner le répertoire cible avant d'uploader l'image.
    </p>
</form>
</td>
<td>
<h2>Fichiers présents</h2>
<div id="retour"></div>
</td>
</tr>
</table>
		
<div id="confirmBox" class="ui-state-highlight ui-corner-all">
	<h2>Répertoire</h2>
    <p><img src="" alt="img" /></p>
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
    Êtes-vous sûr de vouloir supprimer ce message ?</p>
    <form action="" method="get">
    <input type="hidden" name="rep" id="rep" />
    <input type="hidden" name="file" id="file" />
    </form>
    
</div>
			</td>
		</tr>
	</table>
	
	<?php echo $footer;?>
	
</div> 


<!--  Fermeture Wrapper -->
		<script type="text/javascript">
		$(document).ready(function() {
			$("#menu ul li:not(#menu_front,#menu_admin,#menu_decon)").hide();
			$("#left-sidebar *, #zone_des_filtres *").attr("disabled","disabled");

			$(document).ready(function(){

				function reloadImages(){
					$.ajax({        							
						url: 'http://www.sigegis.ugb-edu.com/admin/ScanDirectory/'+$("#formulaire :radio:checked").attr("id"),    					
						success: function(data) {
							$("#retour").html(data);
							 
							
							$('.unlink').on("click",function(){												
								$("#confirmBox").dialog("open");
								$("#rep").val($("#formulaire :radio:checked").attr("id"));
								$("#file").val($(this).parent().children("li a").text());

								$("#confirmBox h2").text("Nom du répertoire: <<"+$("#formulaire :radio:checked").attr("id")+">>");				
								$("#confirmBox img").attr("src",$(this).parent().children("li a").attr("href"));							
							});
							}    
					});
				}
				
				$( "#confirmBox" ).dialog({
					title:"Confirmer la suppression",
				    resizable: false,
				    width:500,
				    autoOpen: false,
				    modal: true,
				    buttons: {
				        "Oui": function() {
				        	$.ajax({        							
								url: 'http://www.sigegis.ugb-edu.com/admin/delete',
								data:'rep='+$("#rep").val()+'&file='+$("#file").val(),    					
								success: function(data) {
									reloadImages();	
								}    
							});
				        	
				            $( this ).dialog( "close" );
				        },
				        "Annuler": function() {
				            $( this ).dialog( "close" );
				        }
				     }
				});
				reloadImages();   
				     
				$("#formulaire :radio").on("change",function(){
					reloadImages();					
				});
			});
		});
		
		</script>		
	<?php echo $scripts;?>
		
</body>
</html>