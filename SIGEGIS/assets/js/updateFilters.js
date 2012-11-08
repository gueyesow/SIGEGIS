
// Sélectionne par défaut le niveau d'agrégation "région" 
$("#niveauAgregation1,#niveauAgregation2").val("region");

// Récupère toutes les années où ont eu lieu un découpage administratif 
$.ajax({            
	url: 'http://www.sigegis.ugb-edu.com/main_controller/getDecoupages',            			         			   
	dataType: 'json',      
	success: function(json) {
		$("#ana_decoupage,#ana_decoupage_localite").empty();
		$.each(json, function(index, value) {         
			$("#ana_decoupage,#ana_decoupage_localite").append('<option value="'+ index +'">'+ value +'</option>');							
		});
		$("#ana_decoupage option:last,#ana_decoupage_localite option:last").attr("selected","selected");
		$("select[name*=niveauAgregation]").change();		
		$("select[name*=ana_decoupage_localite]").change();
		Annees();	      					
	}           
});

$("select[name=ana_decoupage]").on("change",function()
{		
	$("#choixmultipleA,#choixmultipleB,#choixCandidatA,#choixCandidatB").empty();	
	$("select[name=niveauAgregation1]").change();
	Annees();
});

$("select[name*=ana_decoupage_localite]").on("change",function()
{
	
	$.ajax({            // DATES 
		url: "http://www.sigegis.ugb-edu.com/main_controller/getDatesElections?typeElection="+typeElection+"&anneeDecoupage="+$("#ana_decoupage_localite").val(),           			         			      
		dataType: 'json',      
		success: function(json) {
			$elections.empty();
			$.each(json, function(index, value) {         
				$elections.append('<option value="'+ index +'">'+ value +'</option>');
				$tours.change();
			});			
					
			$("#choixMultipleLocalitesA,#choixMultipleLocalitesB,#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
			$("select[name=niveauAgregation2]").change();
		}       
	});			
});

//  si le changement de niveau d'agrégation change avec le 1er mode d'analyse
$("select[name=niveauAgregation1]").on("change",function()
{		
	if ($(this).val()=="pays") { methode="getPays";parametres_analyse+="&niveau=pays&paramAnnee="+$("#ana_decoupage").val();}
	else if ($(this).val()=="region") { methode="getRegions";parametres_analyse+="&niveau=reg";}
	else if ($(this).val()=="departement") { methode="getDepartements";parametres_analyse+="&niveau=dep";}
	else if ($(this).val()=="centre") { methode="getCentres";parametres_analyse+="&niveau=cen";}
	
	$url='http://www.sigegis.ugb-edu.com/main_controller/'+methode+"?typeElection="+typeElection+"&anneeDecoupage="+$("#ana_decoupage").val();

	$.ajax({        							
		url: $url,    
		dataType: 'json', 
		success: function(json) {			
			$("#localite").empty();			
			$.each(json, function(index, value) {         
				$("#localite").append('<option value="'+ index +'">'+ value +'</option>');     
			});		
			$("#choixmultipleA,#choixmultipleB,#choixCandidatA,#choixCandidatB").empty();
			Annees();
		}    
	});			
});

//si le changement de niveau d'agrégation change avec le 2nd mode d'analyse
$("select[name=niveauAgregation2]").on("change",function()
{			
	if ($(this).val()=="pays") { showLocality(); $("#filtrepays").hide();$("#filtreregions").hide();	$("#filtredepartements").hide();	$("#filtrecollectivites").hide();	$("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()=="region") { showLocality();$("#filtreregions").hide();$("#filtredepartements").hide();	$("#filtrecollectivites").hide();	$("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()=="departement") { showLocality();$("#filtredepartements").hide();$("#filtrecollectivites").hide(); $("#filtrecentres").hide();$pays.change();}
	else if ($(this).val()=="centre") { showLocality(); $("#filtrecentres").hide();$pays.change();}
});

// Afficher les options nécessaires pour le choix de la localité
function showLocality(){
	$("#swapListLocality").show();
	$("*[id*=filtre]").show();	
}

// Fournit les différentes années des élections passées
function Annees()
{
	$.ajax({            // DATES 
		url: "http://www.sigegis.ugb-edu.com/main_controller/getDatesElections?typeElection="+typeElection+"&anneeDecoupage="+$("#ana_decoupage").val(),           			         			      
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
			param+=$sources.val();
			if ($("#presidentielle")[0].checked) param+=","+$("#ana_tour").val();
			param+=","+$("#localite").val();
			
			$("#choixmultipleB").children().each(function(){
				if(annees=="") annees+=$(this).text(); else annees+=","+$(this).val();
			});
			
			parametres_analyse="param="+param+"&annees="+annees+"&typeElection="+typeElection;

			if ($("select[name*=niveauAgregation]").val()=="pays") { parametres_analyse+="&niveau=pays"; }
			if ($("select[name*=niveauAgregation]").val()=="region") { parametres_analyse+="&niveau=reg";}
			if ($("select[name*=niveauAgregation]").val()=="departement") {parametres_analyse+="&niveau=dep";}
			if ($("select[name*=niveauAgregation]").val()=="centre") { parametres_analyse+="&niveau=cen";}		
			
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
		param+=$sources.val();
		if ($("#presidentielle")[0].checked) param+=","+$tours.val();
		param+=","+$elections.val()+"&typeElection="+typeElection;
		
		$("#choixMultipleLocalitesB").children().each(function(){
			if(localites=="") localites+=$(this).val(); else localites+=","+$(this).val();
		});
		
		parametres_analyse="param="+param+"&localites="+localites;

		if ($("select[name=niveauAgregation2]").val()=="pays") { parametres_analyse+="&niveau=pays"; }
		if ($("select[name=niveauAgregation2]").val()=="region") { parametres_analyse+="&niveau=reg";}
		if ($("select[name=niveauAgregation2]").val()=="departement") {parametres_analyse+="&niveau=dep";}
		if ($("select[name=niveauAgregation2]").val()=="centre") { parametres_analyse+="&niveau=cen";}		
		
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

