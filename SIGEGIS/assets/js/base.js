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
	    return $.getUrlVars()[name];
	  }
});

	// ---------------------------------------- //
	// 				Partie générique			//
	// ---------------------------------------- //

	var parametres_url="";
	var param="";
	var $pays = $('#pays');
	var $regions = $('#regions');
	var $departements=$("#departements");
	var $collectivites = $('#collectivites');
	var $centres = $('#centres');
	var $sources = $('#sources');
	var $elections = $('#elections');
	var paramLoc="";
	var $tours = $('#tours');
	var mode = "";
	var types_election=["presidentielle","legislative","locale","regionale","municipale","rurale"];
	var types_affichage=["map","bar","pie","grid"];
	
	// ---------------------------------------- //
	// 				Partie analyses				//
	// ---------------------------------------- //
	
	var annees="";
	var mode = "";
	var parametres_analyse="";	 
