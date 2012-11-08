/**
*	Extension de JQuery + Variables globales
*/

$.extend({
	  getUrlVars: function(){
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	      hash = hashes[i].split('=');
	      vars.push(hash[0]);
	      vars[hash[0]] = hash[1];
	    }
	    return vars;
	  },
	  getUrlVar: function(name){
		  if ($.getUrlVars()[name]!=null)
			  return $.getUrlVars()[name];
		  else 
			  return null;
	  }
});

function slideSmoothly(id){
	$('html, body').animate({  
	    scrollTop:$(id).offset().top  
	}, 'slow');  
	return false;  
}

// ---------------------------------------- //
// 				Partie générique			//
// ---------------------------------------- //

var chart1;
var chart2;
var chart3;
var chart4;
var lastPressedButton=null;
var save=false; // Sauvegarde des informations ( PARTIE ANALYSE )
var parametres_url="";
var param="";
var $pays = $('#pays');
var $regions = $('#regions');
var $departements=$("#departements");
var $collectivites = $('#collectivites');
var $centres = $('#centres');
var $sources = $('#sources');
var $elections = $('#elections');
var $tours = $('#tours');
var paramLoc="";
var mode = "";
var types_election=["presidentielle","legislative","locale","regionale","municipale","rurale"];
var titres={"presidentielle":"présidentielle","legislative":"législative","municipale":"municipale","regionale":"régionale","rurale":"rurale"};
var types_affichage=["map","bar","pie","grid"];
var svg;
var niveau=$.getUrlVar("niveau");
var type=$.getUrlVar("type");
var typeElection="presidentielle"; // Attention !!!
var colors=["#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#00ccc6","#bd9695","#ffff51","#ed66a3","#96b200","#ff1951","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300"];

// ---------------------------------------- //
// 				Partie analyses				//
// ---------------------------------------- //

var annees="";
var mode = "";
var parametres_analyse="";	 
