	$.ajax({  // OBTENIR LES SOURCES          
		url: base_url+'filtres/getSources',            			         			      
		dataType: 'json',      
		success: function(json) {
			$sources.empty();
			$.each(json, function(index, value) {         
				$sources.append('<option value="'+ index +'">'+ value +'</option>');     
			});
			$sources.get(0).selectedIndex = 0;        //Permet de selectionner le premier
			$elections.change();	
		}       
	});
	
	if (type) typeElection=type; else typeElection="presidentielle";
	
	$.ajax({            // OBTENIR LES ANNEES D'ELECTION `typeElection` 
		url: base_url+'filtres/getDatesElections',
		data:'typeElection='+typeElection,
		dataType: 'json',      
		success: function(json) {
			if(!json) {$("select:last").change();return;}
			niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
			$elections.empty();
			
			$.each(json, function(index, value) {         
				$elections.append('<option value="'+ index +'">'+ index +'</option>');
				$GRANULARITE[index]=value;				
			});
			
			if($.getUrlVar("year")) $("#elections option[value="+$.getUrlVar("year")+"]").attr("selected","selected");
			else $("#elections option:last").attr("selected","selected");

			$elections.change();
			
			if(type) {if (titre=="presidentielle") $titre="";}
			
			if(niveau=="cen") {nomNiveau="par centre";}
			else if(niveau=="dep") {nomNiveau="départementaux";}
			else if(niveau=="reg") {nomNiveau="régionaux";}
			else if(niveau=="pays") {nomNiveau="par pays";}
			else if (niveau=="globaux") nomNiveau="globaux";
			
			if (! $("#decoupage_annee").length && type) $("#titre").append("Election "+titres[type]+" "+$elections.val()+": résultats "+nomNiveau);			
		}       
	});
	
	$sources.on('change', function() // DECLENCHE TOURS 
	{
		$elections.change();
	});
	
	$elections.on('change', function() // DECLENCHE TOURS 
	{
		niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
		
		if ($GRANULARITE[$elections.val()]=="departement"){ 
			$("#menu_cen").hide();$("#menu_stats_cen").hide();$("#filtrecollectivites").hide();$("#filtrecentres").hide();
		} 
		else {$("#menu_cen").show();$("#menu_stats_cen").show();$("#filtrecollectivites").show();$("#filtrecentres").show();}
		
		var val = $(this).val();   
		if(val != '') {            					           
			$.ajax({            
				url: base_url+'filtres/getTours',            			         			
				data: 'dateElection='+ val,   
				dataType: 'json',      
				success: function(json) {
					$tours.empty();

					$.each(json, function(index, value) {      
						$tours.append('<option value="'+ index +'">'+ value +'</option>');							
					});
					
					$tours.trigger("liszt:updated");
					
					$tours.change();
					
					if (! $("#decoupage_annee").length && type) $("#titre").text("Election "+titres[type]+" "+$elections.val()+": résultats "+nomNiveau);
					
					if ($("#poidsElectoralRegions").length>0) $("#titre").text("Election "+titres[type]+" "+$elections.val()+": taux de participation");
					
					$('#menu li a:not(#menu_front a,#menu_admin a,#menu_decon a,#menu_apropos a,#menu_analyse a)').each(function(){								
							
						if( $(this).attr("href").indexOf("year")==-1 )	
							$(this).attr("href",$(this).attr("href")+"&year="+$elections.val());
						else 
							$(this).attr("href",$(this).attr("href").substr(0,$(this).attr("href").indexOf('year')+5)+$elections.val());
						
						url=$(this).attr("href");
						year="";chaine="";
														
						if( $.getUrlVar("map") && $.getUrlVar("grid") && $.getUrlVar("pie") && $.getUrlVar("bar") ) 
							{url+="&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid");}

						if( $.getUrlVar("year") ) {
							if( $.getUrlVar("year")==$elections.val() )
								chaine="&year="+$.getUrlVar("year");
							else   
								chaine="&year="+$elections.val();

							if(url.indexOf("year")==-1) 
								$(this).attr("href",url+chaine);
							else
								$(this).attr("href",url.substr(0,url.indexOf("&year"))+chaine);
						}
						
					});
										
				}           
			});       
		} // SI PRESIDENTIELLE SINON
		
		if(!$("#presidentielle")[0].checked){
			val = $elections.val();
			
			if(val != '') {               
				$.ajax({            
					url: base_url+'filtres/getPays',
					data: 'paramAnnee='+ val,
					dataType: 'json',      
					success: function(json) {      
						$pays.empty();

						$.each(json, function(index, value) {         
							$pays.append('<option value="'+ index +'">'+ value +'</option>');     
						});
						$pays.change();
					}           
				});       
			}    
			$pays.change();
			$pays.trigger("liszt:updated");
		}
	});		
	
	$tours.on('change', function()   // DECLENCHE PAYS
	{
		niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
		var val = $elections.val();

		if(val != '') {               
			$.ajax({            
				url: base_url+'filtres/getPays',
				data: 'paramAnnee='+ val,
				dataType: 'json',      
				success: function(json) {      
					$pays.empty();
					//$pays.append('<option value=""></option>');
					$.each(json, function(index, value) {         
						$pays.append('<option value="'+ index +'">'+ value +'</option>');     
					});
					$pays.change();   
					$pays.trigger("liszt:updated");
				}           
			});       
		}    
	});
	
	$pays.on('change', function() // DECLENCHE REGIONS   
	{
		niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
		var val = $(this).val();     
		if(val != '') {           
			           
			$.ajax({            
				url: base_url+'filtres/getRegions',            			         			
				data: 'idPays='+ val,   
				dataType: 'json',      
				success: function(json) {
					$regions.empty();
					//$regions.append('<option value=""></option>');
					if ($("select[name=niveauAgregation2]").length>0) $("#choixMultipleLocalitesA").empty();
					$.each(json, function(index, value) {         
						$regions.append('<option value="'+ index +'">'+ value +'</option>');
						if ($("select[name=niveauAgregation2]").val() == "region") $("#choixMultipleLocalitesA").append('<option value="'+ index +'">'+ value +'</option>');								
					});
					$regions.change();
					$regions.trigger("liszt:updated");

					if ($("select[name=niveauAgregation2]").val() == "pays") {
						$pays.children().each(function() {         
							$("#choixMultipleLocalitesA").append('<option value="'+ $(this).val() +'">'+ $(this).text() +'</option>');								
						});
					}
				}           
			});       
		}    
	});
	
	$regions.on('change', function() // DECLENCHE DEPARTEMENTS  
	{
		niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
		var val = $(this).val();      
		if(val != '') {            					           
			$.ajax({            
				url: base_url+'filtres/getDepartements',            			         			
				data: 'idRegion='+ val,   
				dataType: 'json',      
				success: function(json) 
				{
					$departements.empty();
					if ($("select[name=niveauAgregation2]").val() == "departement") $("#choixMultipleLocalitesA").empty();
					$.each(json, function(index, value) {         
					$departements.append('<option value="'+ index +'">'+ value +'</option>');
					if ($("select[name=niveauAgregation2]").val() == "departement") $("#choixMultipleLocalitesA").append('<option value="'+ index +'">'+ value +'</option>');
					});
									
					$departements.change();
					$departements.trigger("liszt:updated");
				}           
			});       
		}    
	});
	
	$departements.on('change', function() // DECLENCHE COLLECTIVITES 
	{
		if ($GRANULARITE[$elections.val()]=="centre")
		{
			niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
			var val = $(this).val();    
			if(val != '') {					            					           
				$.ajax({            
					url: base_url+'filtres/getCollectivites',            			         			
					data: 'idDepartement='+ val,      
					dataType: 'json',      
					success: function(json) {      
						$collectivites.empty();
	
						$.each(json, function(index, value) {         
							$collectivites.append('<option value="'+ index +'">'+ value +'</option>');     
						});         
						$collectivites.change();
						$collectivites.trigger("liszt:updated");
					}           
				});       
			}
		} else {if (niveau="cen") {niveau="dep";if (! $("#decoupage_annee").length && type) $("#titre").text("Election "+titres[type]+" "+$elections.val()+": résultats départementaux");}}
	});

	$collectivites.on('change', function()  // DECLENCHE CENTRES 
	{
		niveau=$.getUrlVar("niveau");type=$.getUrlVar("type");
		var val = $(this).val();   
		if(val != '') {					            					           
			$.ajax({            
				url: base_url+'filtres/getCentres',            			         			
				data: 'idCollectivite='+ val,      
				dataType: 'json',      
				success: function(json) {      
					$centres.empty();
					if ($("select[name=niveauAgregation2]").val() == "centre") $("#choixMultipleLocalitesA").empty();
					$.each(json, function(index, value) {         
						$centres.append('<option value="'+ index +'">'+ value +'</option>');
						if ($("select[name=niveauAgregation2]").val() == "centre") $("#choixMultipleLocalitesA").append('<option value="'+ index +'">'+ value +'</option>');
					});
					$centres.change(); // Permet de specifier un changement (:selected)
					$centres.trigger("liszt:updated");
				}           
			});       
		}    
		
	});
	