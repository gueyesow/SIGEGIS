/*
*	Extension de JQuery + Variables globales
*/

//Permet l'obtention des parametres d'url  
$.extend({
	  getUrlVars: function(){
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	      hash = hashes[i].split('=');
	      vars.push(hash[0]); // inutile 
	      vars[hash[0]] = hash[1];
	    }
	    return vars;
	  },
	  getUrlVar: function(name){
		  //if ($.getUrlVars()[name]!=null)
			  return $.getUrlVars()[name];
		  //else 
			//  return null;
	  }
});

// Effet slide
function slideSmoothly(id){
	$('html, body').animate({  
	    scrollTop:$(id).offset().top  
	}, 'slow');  
	return false;  
}

// ---------------------------------------- //
// 				Partie générique			//
// ---------------------------------------- //

var chart1; // Les quatre futurs objets Highcharts 
var chart2;
var chart3;
var chart4;
var lastPressedButton1=null; // La derniere action effectuee dans la partie comparaison des resultats (inutile) 
var lastPressedButton2=null;
var request1OrRequest2;// La derniere requete effectuee dans la partie comparaison des resultats (#ouvrir ou #comparer) 
var save=false; // Sauvegarde des informations ( PARTIE ANALYSE )

var param=""; // Les parametres a transmettre aux differents modeles (souvent issus des filtres)
var $pays = $('#pays'); // Le filtre pays et les autres le suivent 
var $regions = $('#regions');
var $departements=$("#departements");
var $collectivites = $('#collectivites');
var $centres = $('#centres');
var $sources = $('#sources');
var $elections = $('#elections');
var $tours = $('#tours');

var mode = ""; // Ensemble cle=>valeur permettant de savoir quel mode de representation des donnees est active, "cle" prend l'ID du mode et "valeur" prend yes ou no 
var types_election=["presidentielle","legislative","locale","regionale","municipale","rurale"];
var titres={"presidentielle":"présidentielle","legislative":"législative","municipale":"municipale","regionale":"régionale","rurale":"rurale"}; // Les titres des elections (servira a la transformation des ID d'elections en noms valides)
var types_affichage=["map","bar","pie","grid","line"]; // Les differents modes d'affichage (inutile)
var svg; // La future image svg representant une carte du Senegal 
var niveau=$.getUrlVar("niveau");
var type=$.getUrlVar("type"); // Le parametre d'url 'type' correspondant a un type d'election 
//var typeElection="presidentielle"; // Attention !!!
var colors=["#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#00ccc6","#bd9695","#ffff51","#ed66a3","#96b200","#ff1951","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300"]; // Les couleurs disponibles pour les diagrammes et les candidats 

//----------------------------------------------------------------------//
// Cette variable est primordiale pour le bon fonctionnement des scripts /	
//----------------------------------------------------------------------//
var base_url='http://sigegis.ugb-edu.com/';
//----------------------------------------------------------------------//

// ---------------------------------------- //
// 				Partie analyses				//
// ---------------------------------------- //

var annees=""; // Utile (Analyse suivant annees)
var parametres_analyse="";	 // Importante
