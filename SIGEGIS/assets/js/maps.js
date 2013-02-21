$(function () {
	// Mise en forme - Activation des Options
	
	$("#menu_globaux,#menu_pays,#menu_cen").remove();
	$("#bloc_horizontal_filtres *[id*=filtre]:gt(2)").hide();	
	$("#line,:checkbox:not(#map)").attr("disabled","disabled");
	$("#map").attr("checked","checked");

	$("#map").on("click",function(){mode="";
		$("#types_affichage input:not(#map)").each(function(){
			idmode=""+$(this).attr("id");
			valeur=($(this).attr("checked")=="checked")?"yes":"no";		
			mode+="&"+idmode+"="+valeur;			
		});
		
		if ($.getUrlVar("niveau")!="reg" && $.getUrlVar("niveau")!="dep") mode+="&niveau=dep";
		else mode+="&niveau="+$.getUrlVar("niveau");
		
		if(!$.getUrlVar("year")) mode+="&year="+$elections.val();
		else mode+="&year="+$.getUrlVar("year");
		window.location.href=base_url+"visualiser/resultats?type="+$.getUrlVar("type")+mode;
	});

	
	function create_map(year_decoupage){

		svg = $('#senmaps').svg('get');
		if (svg) $('#senmaps').svg('destroy');
		$('#senmaps').svg();
		svg = $('#senmaps').svg('get');
		if (year_decoupage) svg.load(base_url+'assets/images/snmaps/'+niveau+'_'+year_decoupage+'.svg',{changeSize: true,onLoad: loadDone});
		else return false;
	}

	function loadDone(svg, error) {
		if (error) {alert("Erreur: la carte correspondante est introuvable");return false;}
		refreshAll();
		$('svg:not(g,path)').hover(function(e){$('.tooltip2').remove();});
		
		$("[id^=D],[id^=R]").hover(function(e){					
			a=$(this);
			$('<p class="tooltip2"></p>')
			.html($(a).attr("nomlieu")+"<br />"+$(a).attr("nomcandidat")+"<br />Voix :"+$(a).attr("resultat")+"<br />% :"+$(a).attr("pourcentage"))
			.appendTo('body')
			.css('top', (e.pageY - 10) + 'px')
			.css('left', (e.pageX + 20) + 'px').show();										
		}, function() {
			// Hover out
			$('.tooltip2').remove();
		});
		
	}
	
	function refreshAll(){
		param=$sources.val();
		if(type=="presidentielle") param+=","+$tours.val();
					
		$paramCharts='param='+param+'&typeElection='+type+'&annees='+$elections.val();
		if (niveau!="globaux") $paramCharts+='&niveau='+niveau;

		$.ajax({        							
			url: base_url+'filtres/getCandidatsAnnee',    
			data:$paramCharts,        					     
			success: function(json) {		
				data=JSON.parse(json);
				tab = new Object();	i=0;									

				$("#winners").empty();
				$("#winners").append("<table>");
				$.each(data, function(value) {
					tab[value]=colors[i++];
					$("#winners").append("<tr><!--td>"+value+"</td--><td>"+data[value]+"</td><td><span class='ui-corner-all' style='opacity:0.7;width:70px;display:inline-block;height:10px;background:"+tab[value]+"'></span></td></tr>");
				});
				$("#winners").append("</table>");
				param=$sources.val()+","+$elections.val();
				if(type=="presidentielle") param+=","+$tours.val();														

				$paramCharts='param='+param+'&typeElection='+type;
				if (niveau!="globaux") $paramCharts+='&niveau='+niveau;

				$.ajax({        							
					url: base_url+'visualiser/getWinnersLocalites',    
					data:$paramCharts,        					     
					success: function(json) {
						
						data=JSON.parse(json);
						if (!data.length) $('#senmaps').svg('destroy'); //si pas de données detruire map						
											
						$.each(data, function(value) {									
							$("#"+data[value].idLieu).css("fill",tab[data[value].id]);
							$("#"+data[value].idLieu).attr("nomcandidat",data[value].name);
							$("#"+data[value].idLieu).attr("nomlieu",data[value].nomlieu);
							$("#"+data[value].idLieu).attr("resultat",data[value].voix);	
							$("#"+data[value].idLieu).attr("pourcentage",data[value].percent.toFixed(2));
						});											
					}    
				});																									
			}    
		});
	}

if($.getUrlVar("bar")=="no") {$("#chartdiv1").hide();$("#bar").removeAttr("checked");} else  if($.getUrlVar("bar")=="yes") {$("#chartdiv1").show();$("#bar").attr("checked","checked");}
if($.getUrlVar("pie")=="no") {$("#chartdiv2").hide();$("#pie").removeAttr("checked");} else  if($.getUrlVar("pie")=="yes"){$("#chartdiv2").show();$("#pie").attr("checked","checked");} else $("#chartdiv2").hide();
if($.getUrlVar("grid")=="no") {$("#theGrid").hide();$("#grid").removeAttr("checked");} else  if($.getUrlVar("grid")=="yes") {$("#theGrid").show();$("#grid").attr("checked","checked");}

// Affiner les options pour les élections locales (regionales,municipales,rurales)

$.each(types_election,function(){  
	if (type==""+this){
		$("#"+this).attr("checked","checked");
		if(""+this=="locale"||""+this=="municipale"||""+this=="regionale"||""+this=="rurale") {
			$("#types_elections").append(
				"<fieldset id='ss_locales'><legend>Elections locales</legend>"+
				"<input id='municipale' type='radio' name='radio3' /><label for='municipale'>Municipales</label><br />"+
				"<input id='regionale' type='radio' name='radio3' /><label for='regionale'>Régionales</label><br />"+
				"<input id='rurale' type='radio' name='radio3' /><label for='rurale'>Rurales</label></fieldset>");
			$("#locale").attr("checked","checked");$("#"+this).attr("checked","checked");
		}
	}
});

// Prise en compte du changement du type d'élection

$("#types_elections input").on("change",function() {
	var idelection=""+$(this).attr("id");
	
	$.each(types_election,function(){		
		if (idelection==""+this )
		{						
			$("#types_affichage input").each(function(){
				idmode=""+$(this).attr("id");
				valeur=($(this).attr("checked")=="checked")?"yes":"no";		
				mode+="&"+idmode+"="+valeur;	
			});
			
			if(niveau) mode+="&niveau="+niveau;
			
			if(  this !="regionale" && this!="municipale" && this!="rurale" ) {
				if (mode) window.location.href=base_url+"visualiser/getMap?type="+this+mode;
				else window.location.href=base_url+"visualiser/getMap?type="+this;				
			}
			
			$("#ss_locales :input").on("click",function(){
				if (mode) window.location.href=base_url+"visualiser/getMap?type="+$(this).attr("id")+mode;
				else window.location.href=base_url+"visualiser/getMap?type="+$(this).attr("id");
			});
		}
	});
});
		
if (type != "presidentielle") $("#filtretours").remove();
	
	param=$sources.val()+","+$elections.val();
	
	if(type=="presidentielle") param+=","+$tours.val();								
	
	$pays.on("change",function(){
		$.ajax({        	
		method:'GET',						
		url: base_url+'filtres/getDecoupagePays',
		data:'idPays='+$(this).val(),           					     
		success: function(data) {
			create_map(data);											
		}    
		});
	});	
	
	$elections.on("change",function(){
		
		$('#menu li a:not(#menu_front a,#menu_apropos a,#menu_analyse a,#menu_resultats a:first)').each(function(){														
			if( $(this).text()!=$('#menu a:first').text()  && $(this).text()!=$('#menu a:last').text() && $(this).text()!=$('#menu_admin a').text())
			{
				if( $(this).attr("href").indexOf("year")==-1 )	$(this).attr("href",$(this).attr("href")+"&year="+$elections.val());
				else $(this).attr("href",$(this).attr("href").substr(0,$(this).attr("href").indexOf('year')+5)+$elections.val());
			}
				
				
				url=$(this).attr("href"); year="";chaine="";
												
				if( $(this).text()!=$('#menu a:first').text()  && $(this).text()!=$('#menu a:last').text() && $(this).text()!=$('#menu_admin a').text()){
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
				}
			});					      
	
	});
});		  	
