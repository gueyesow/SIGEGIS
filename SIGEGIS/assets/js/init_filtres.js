	

	$.ajax({  // OBTENIR LES SOURCES          
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getSources',            			         			      
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
	
	$.ajax({            // OBTENIR LES ANNEES D'ELECTION 
		url: 'http://www.sigegis.ugb-edu.com/main_controller/getDatesElections',
		data:'typeElection='+$.getUrlVar("type"),
		dataType: 'json',      
		success: function(json) {
			$elections.empty();
			$.each(json, function(index, value) {         
				$elections.append('<option value="'+ index +'">'+ value +'</option>');     
			});
			
			if($.getUrlVar("year")) $("#elections option[value="+$.getUrlVar("year")+"]").attr("selected","selected");
			else $("#elections option:last").attr("selected","selected");

			$elections.change();         
			titres={"presidentielle":"présidentielle","legislative":"législative","municipale":"municipales","regionale":"régionales"};
			if(type=""+$.getUrlVar("type")) {if (titre==="presidentielle") $titre="";}
			if($.getUrlVar("niveau")==="cen") {niveau="par centre";}
			else if($.getUrlVar("niveau")==="dep") {niveau="départementaux";}
			else if($.getUrlVar("niveau")==="reg") {niveau="régionaux";}
			else if($.getUrlVar("niveau")==="pays") {niveau="par pays";}
			else niveau="globaux";
			$("#titre").append("Résultats "+niveau+" de l'élection "+titres[type]+" de "+$elections.val());						
		}       
	});
	
	$elections.on('change', function() // DECLENCHE TOURS 
			{
				val = $(this).val();   
				if(val != '') {            					           
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getTours',            			         			
						data: 'dateElection='+ val,   
						dataType: 'json',      
						success: function(json) {
							$tours.empty();
							$.each(json, function(index, value) {         
								$tours.append('<option value="'+ index +'">'+ value +'</option>');							
							});         							
							$tours.change();
							$("#titre").text("Résultats "+niveau+" de l'élection "+titres[type]+" de "+$elections.val());
							$('#menu-css a').each(function(){
								
								if( !$.getUrlVar("year") ){			
									if( $(this).text()!=$('#menu-css a:first').text() && $(this).text()!=$('#menu-css a:eq(1)').text() )
									if( $(this).attr("href").indexOf("year")===-1 )
									$(this).attr("href",$(this).attr("href")+"&year="+$elections.val());
									else $(this).attr("href",$(this).attr("href").substr(0,$(this).attr("href").indexOf('year')+5)+$elections.val());
								}									
								
								url=$(this).attr("href");
								year="";chaine="";
								
								if( $(this).text()!=$('#menu-css a:first').text() && $(this).text()!=$('#menu-css a:eq(1)').text() ){
									if( $.getUrlVar("map") && $.getUrlVar("grid") && $.getUrlVar("pie") && $.getUrlVar("bar") ) 
										url+="&map="+$.getUrlVar("map")+"&bar="+$.getUrlVar("bar")+"&pie="+$.getUrlVar("pie")+"&grid="+$.getUrlVar("grid");

									if( $.getUrlVar("year") ) {
										if( $.getUrlVar("year")===$elections.val() )
											chaine="&year="+$.getUrlVar("year");
										else   
											chaine="&year="+$elections.val();

										if(url.indexOf("year")===-1) 
											$(this).attr("href",url+chaine);
										else
											$(this).attr("href",url.substr(0,url.indexOf("&year"))+chaine);
									}
								}
							});
						}           
					});       
				}    
	});		
	
	$tours.on('change', function()   // DECLENCHE PAYS
			{
				val1 = $elections.val();
		
				if(val1 != '') {               
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getPays',
						data: 'paramAnnee='+ val1,
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
	});
	
	$pays.on('change', function() // DECLENCHE REGIONS   
			{
				val1 = $(this).val();      
				if(val1 != '') {           
					           
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getRegions',            			         			
						data: 'idPays='+ val1,   
						dataType: 'json',      
						success: function(json) {
							$regions.empty();
							$.each(json, function(index, value) {         
								$regions.append('<option value="'+ index +'">'+ value +'</option>');     
							});
							$regions.change();         
						}           
					});       
				}    
	});
	
	$regions.on('change', function() // DECLENCHE DEPARTEMENTS  
			{
				val1 = $(this).val();      
				if(val1 != '') {            					           
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getDepartements',            			         			
						data: 'idRegion='+ val1,   
						dataType: 'json',      
						success: function(json) 
						{
							$departements.empty();
							$.each(json, function(index, value) {         
							$departements.append('<option value="'+ index +'">'+ value +'</option>');     
							});
							$departements.change();         
						}           
					});       
				}    
	});
	
	$departements.on('change', function() // DECLENCHE COLLECTIVITES 
			{
				val2 = $(this).val();    
				if(val2 != '') {					            					           
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getCollectivites',            			         			
						data: 'idDepartement='+ val2,      
						dataType: 'json',      
						success: function(json) {      
							$collectivites.empty();
							$.each(json, function(index, value) {         
								$collectivites.append('<option value="'+ index +'">'+ value +'</option>');     
							});         
							$collectivites.change();
						}           
					});       
				}    
	});

	$collectivites.on('change', function()  // DECLENCHE CENTRES 
			{
				val3 = $(this).val();   
				if(val3 != '') {					            					           
					$.ajax({            
						url: 'http://www.sigegis.ugb-edu.com/main_controller/getCentres',            			         			
						data: 'idCollectivite='+ val3,      
						dataType: 'json',      
						success: function(json) {      
							$centres.empty();
							$.each(json, function(index, value) {         
								$centres.append('<option value="'+ index +'">'+ value +'</option>');     
							});
							$centres.change(); // // Permet de specifier un changement (:selected)         
						}           
					});       
				}    
	});

	