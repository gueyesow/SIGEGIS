//-------------------------------------------------
// 	Partie commune aux deux modes de comparaison
//-------------------------------------------------

// Sélectionne par défaut le niveau d'agrégation "région" 
$("#niveauAgregation1,#niveauAgregation2").val("region");

// Si la source change, on remet tout a zero
$sources.on("change",function(){ $("select[name*=decoupage_]").change(); });

//-------------------------------------
// 	Mode 1:Analyse suivant les annees 
//-------------------------------------

//Fournit les années des élections passées
function Annees()
{
	$.ajax({            // DATES 
		url: base_url+"filtres/getDatesElections?typeElection="+typeElection+"&anneeDecoupage="+$("#decoupage_annee").val(),           			         			      
		dataType: 'json',      
		success: function(json) {
			//if (!json) return false;
			$("#choixmultipleA").empty();
			$.each(json, function(index, value) {         
				$("#choixmultipleA").append('<option value="'+ index +'">'+ value +'</option>');
			});
		}       
	});
}

// Initialisation de l'utilitaire 
$.ajax({            
	url: base_url+'filtres/getDecoupages',            			         			   
	dataType: 'json',      
	success: function(json) {
		
		$("#decoupage_annee,#decoupage_localite").empty();
		
		$.each(json, function(index, value) {         
			$("#decoupage_annee,#decoupage_localite").append('<option value="'+ index +'">'+ value +'</option>'); // charge les 2 filtres en meme temps							
		});
		
		$("#decoupage_annee option:last,#decoupage_localite option:last").attr("selected","selected"); // selectionne le dernier decoupage connu
		
		$("select[name*=niveauAgregation]").change();

		$("select[name=decoupage_localite]").change();

		Annees();	      					
	}           
});

// Raffraîchir les composants au changement de la carte administrative
$("select[name=decoupage_annee]").on("change",function()
{		
	$("#accordion_item1 select[id*='choix']").empty();	
	$("select[name=niveauAgregation1]").change();
	Annees();
});

//  si le changement de niveau d'agrégation change avec le 1er mode d'analyse
$("select[name=niveauAgregation1]").on("change",function()
{		
	if ($(this).val()=="pays") { methode="getPays";parametres_analyse+="&niveau=pays&anneeDecoupage="+$("#decoupage_annee").val();}
	else if ($(this).val()=="region") { methode="getRegions";parametres_analyse+="&niveau=reg";}
	else if ($(this).val()=="departement") { methode="getDepartements";parametres_analyse+="&niveau=dep";}
	else if ($(this).val()=="centre") { methode="getCentres";parametres_analyse+="&niveau=cen";}
	
	$url=base_url+"filtres/"+methode+"?typeElection="+typeElection+"&anneeDecoupage="+$("#decoupage_annee").val();

	$.ajax({        							
		url: $url,    
		dataType: 'json', 
		success: function(json) {			
			//if (!json) return false;
			$("#localite").empty();			
			$.each(json, function(index, value) {         
				$("#localite").append('<option value="'+ index +'">'+ value +'</option>');     
			});		

			$("#accordion_item1 select[multiple='multiple']").empty();
			Annees();
		}    
	});			
});

$("#ana_tour").on("change",function()
{
	$("#choixCandidatA,#choixCandidatB").empty();
	$("#choixmultipleB").change();
});

// Liste les candidats compte tenu des parametres choisis par l'utilisateur
$("#choixmultipleB").on("change",function()
		{
			$("#choixCandidatA,#choixCandidatB").empty();
			param="";annees=[];
			param+=$sources.val();
			if ($("#presidentielle")[0].checked) param+=","+$("#ana_tour").val();
			param+=","+$("#localite").val();

			$("#choixmultipleB").children().each(function(){ annees.push($(this).val()); });  // Objectif: 2007,2012,...

			parametres_analyse="param="+param+"&annees="+annees.join(",")+"&typeElection="+typeElection;

			if ($("select[name=niveauAgregation1]").val()=="pays") { parametres_analyse+="&niveau=pays"; }
			if ($("select[name=niveauAgregation1]").val()=="region") { parametres_analyse+="&niveau=reg";}
			if ($("select[name=niveauAgregation1]").val()=="departement") {parametres_analyse+="&niveau=dep";}
			if ($("select[name=niveauAgregation1]").val()=="centre") { parametres_analyse+="&niveau=cen";}
			
			$.ajax({
				url: base_url+"filtres/getCandidatsAnnee",
				data:parametres_analyse,
				dataType:'json',        					     
				success: function(json) {		
					$.each(json, function(index, value) {						
						$("#choixCandidatA").append('<option value="'+ index +'">'+ value +'</option>');						     
					});																				         
				}    
			});
		});


//-----------------------------------------
//	Mode 2: Analyse suivant les localités  
//-----------------------------------------

$("select[name=decoupage_localite]").on("change",function()
{
	$.ajax({            // DATES
		url: base_url+"filtres/getDatesElections?typeElection="+typeElection+"&anneeDecoupage="+$("#decoupage_localite").val(),
		dataType: 'json',
		success: function(json) {
			//if (!json) return false;
			$elections.empty();
			$.each(json, function(index, value) {
				$elections.append('<option value="'+ index +'">'+ value +'</option>');
			});
			$elections.change();

			$("#accordion_item2 select[multiple='multiple']").empty();
			$("select[name=niveauAgregation2]").change();
		}
	});
});

//si le changement de niveau d'agrégation change avec le 2nd mode d'analyse
$("select[name=niveauAgregation2]").on("change",function()
{			
	$("#accordion_item2 select[multiple='multiple']").empty();
	if ($(this).val()=="pays") { showLocality(); $("#filtrepays").hide();$("#filtreregions").hide();$("#filtredepartements").hide();$("#filtrecollectivites").hide();$("#filtrecentres").hide();}
	else if ($(this).val()=="region") { showLocality();$("#filtreregions").hide();$("#filtredepartements").hide();$("#filtrecollectivites").hide();$("#filtrecentres").hide();}
	else if ($(this).val()=="departement") { showLocality();$("#filtredepartements").hide();$("#filtrecollectivites").hide(); $("#filtrecentres").hide();}
	else if ($(this).val()=="centre") { showLocality(); $("#filtrecentres").hide();}
	$pays.change();
});

// Afficher les options nécessaires pour le choix de la localité
function showLocality(){
	$("#accordion_item2 *[id*=filtre]").show();	
}

/**
 * ON DOIT PRENDRE EN COMPTE LES LOCALITES SELECTIONNEES POUR PREVOIR LE CAS D'UNE ELECTION LOCALE OU LEGISLATIVE
 */
$("#choixMultipleLocalitesB").on("change",function()
{
	$("#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
	localites=[];
	param=$sources.val();
	if ($("#presidentielle")[0].checked) param+=","+$tours.val();
	param+=","+$elections.val()+"&typeElection="+typeElection;
	
	$("#choixMultipleLocalitesB").children().each(function(){ localites.push($(this).val()); });  // Objectif: region1,region2,...
	
	parametres_analyse="param="+param+"&localites="+localites.join(",");
	if ($("select[name=niveauAgregation2]").val()=="pays") { parametres_analyse+="&niveau=pays"; }
	if ($("select[name=niveauAgregation2]").val()=="region") { parametres_analyse+="&niveau=reg";}
	if ($("select[name=niveauAgregation2]").val()=="departement") {parametres_analyse+="&niveau=dep";}
	if ($("select[name=niveauAgregation2]").val()=="centre") { parametres_analyse+="&niveau=cen";}		

	$.ajax({        							    
		url: base_url+"filtres/getCandidatsLocalite",
		data:parametres_analyse,
		dataType:'json',        					     
		success: function(json) {
			$.each(json, function(index, value) {						
				$("#choixCandidatLocaliteA").append('<option value="'+ index +'">'+ value +'</option>');						     
			});																				         
		}    
	});
});

// Si le tour change, on cherche les candidats du tour correspondant
$tours.on("change",function()
{
	$("#choixCandidatLocaliteA,#choixCandidatLocaliteB").empty();
	$("#choixMultipleLocalitesB").change();
});

// Recharge les localites et efface les autres SwapLists
$elections.on("change",function()
{
	$("select[name=niveauAgregation2]").change();
});