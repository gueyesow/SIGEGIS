$("input[id=valider],input[id=validerLocalite]").button();
$("#myRadio").buttonset();

$('#menu-css li a').hover(   
		  
        function() {   
  
            $(this).css('padding', '5px 15px')   
         .stop()
         .animate({'backgroundColor':'rgba(0,0,0,0.5)'},   
                     'fast');   
         /*.animate({'paddingLeft'    : '17px',   
                     'paddingRight' : '17px',   
                     'backgroundColor':'rgba(0,0,0,0.5)'},   
                     'fast');*/   
        },   
  
        function() {   
  
            $(this).css('padding', '5px 15px')   
         .stop()   
         .animate({'paddingLeft'    : '15px',   
                    'paddingRight'      : '15px',   
                    'backgroundColor' :'rgba(0,0,0,0.2)'},   
                    'fast');   
  
    }).mousedown(function() {   
  
        $(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.1)'}, 'fast');   
  
    }).mouseup(function() {   
  
        $(this).stop().animate({'backgroundColor': 'rgba(0,0,0,0.5)'}, 'fast');   
    });   
  
	$("#zone_des_filtres").addClass("ui-state-default ui-corner-all");
	$(".zone_des_options").addClass("ui-state-default ui-corner-all");
	
	$("#chartdiv2").hide();

	$("#accordion").accordion({ header: "h3" });
	
	$("#accordion div div[id*='ana']").css('clear','both');
	