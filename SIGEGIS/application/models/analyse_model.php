<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author Amadou SOW && Abdou Khadre GUEYE | DESS 2ITIC 2011-2012
 *
 */
class Analyse_model extends CI_Model{
	private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getBarAnalyserAnnee($balise){
			
		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
			
		if(!empty($_GET['param']) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
				
			$v=0;

			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
			else $parametres3="null";
				
			$colonnesBDD=array("rp.idSource","election.tour",$parametres3);
				
			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat,rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
					
				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
					
				for($i=0;$i<sizeof($params);$i++) {
					if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}

				$theYear="";
				foreach ($listeAnnees as $lAnnee){
					if ($theYear=="") $theYear.=" AND ( YEAR(dateElection)='".$lAnnee."'";
					else $theYear.= " OR YEAR(dateElection)='".$lAnnee."'";
				}
				$requete.=$theYear.")";
					
				$requete.=" AND rp.idCandidature=".$leCandidat." ORDER BY dateElection ASC";
					
				$resultats=$this->db->query($requete)->result();
					
				$i=0;$j=0;
					
				$ordonnee="";
					
				foreach ($resultats as $resultat){
					if (!($j++)) $ordonnee.=$resultat->nbVoix;
					else $ordonnee.=",$resultat->nbVoix";
				}

				$tableauResultats[]="{name:'$resultat->nomCandidat', data:[".$ordonnee."]}";
			}
			$abscisse=$_GET["listeAnnees"];
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Résultats ";
		if ($niveau=="cen") {
			$titre_niveau.="par centre ";$sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			$titre_niveau.="départementaux ";$sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			$titre_niveau.="régionaux ";$sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			$titre_niveau.="par pays ";$sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";

		//$titre_niveau.="de l'élection présidentielle de ".$resultats[0]->annee;


		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv1")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";


		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			if ($series=="") $series.=$resultats=$tableauResultats[$j];
			else $series.=",".$resultats=$tableauResultats[$j];
		}


		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//

		return "<script type='text/javascript'>
		$(function () {
		var chart;
		$(document).ready(function() {
		chart = new Highcharts.Chart({
		chart: {
		renderTo: '$balise',
		type: 'column'
		},
		title: {
		text: \"$titre\"
		},
		subtitle: {
		text: \"$sous_titre\"
		},
		xAxis: {
		categories: [$abscisse],
	
		labels: {
		rotation: -40,
		align: 'right',
		style: {
		width:20,
		fontSize: '12px',
		fontFamily: 'Verdana, sans-serif'
		}
		}
		},
		yAxis: {
		min: 0,
		title: {
		text: 'NbVoix ($unite)'
		}
		},
		exporting: {
		//enabled: false
		url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
		},
		legend: {
		layout: 'vertical',
		backgroundColor: '#FFFFFF',
		align: 'right',
		verticalAlign: 'top',
		floating: true,
		shadow: true
		},
		tooltip: {
		formatter: function() {
		return  this.y;
		}
		},
	
		plotOptions: {
		column: {
		pointPadding: 0.2,
		borderWidth: 0,
		dataLabels: {
		enabled: true
		}
		}
		},
		credits: {
		enabled: false
		},
		series:[$series]
	
		});
		});
		});
		</script>";
	} // ...............  Fin de getBarAnalyserAnnee ...............


	/**
	 * Cette fonction retourne le code JavaScript du Pie chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getPieAnalyserAnnee($balise){


		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";



		if(!empty($_GET['param']) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);

			$v=0;

			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
			else $parametres3="null";

			$colonnesBDD=array("rp.idSource","election.tour",$parametres3);

			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat,rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

				for($i=0;$i<sizeof($params);$i++) {
					if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}

				$theYear="";
				foreach ($listeAnnees as $lAnnee){
					if ($theYear=="") $theYear.=" AND ( YEAR(dateElection)='".$lAnnee."'";
					else $theYear.= " OR YEAR(dateElection)='".$lAnnee."'";
				}
				$requete.=$theYear.")";

				$requete.=" AND rp.idCandidature=".$leCandidat." ORDER BY dateElection ASC";

				$resultats=$this->db->query($requete)->result();

				$i=0;$j=0;

				$ordonnee="";

				foreach ($resultats as $resultat){
					if (!($j++)) $ordonnee.=$resultat->nbVoix;
					else $ordonnee.=",$resultat->nbVoix";
				}

				$tableauResultats[]="{name: '$resultat->nomCandidat',y: $resultat->nbVoix}";
				//,sliced: true,selected: true
			}
			$abscisse=$_GET["listeAnnees"];
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Résultats ";
		if ($niveau=="cen") {
			$titre_niveau.="par centre ";$sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			$titre_niveau.="départementaux ";$sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			$titre_niveau.="régionaux ";$sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			$titre_niveau.="par pays ";$sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";

		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv2")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";


		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			if ($series=="") $series.=$resultats=$tableauResultats[$j];
			else $series.=",".$resultats=$tableauResultats[$j];
		}
		return "<script>$(function () {
		var chart;
		$(document).ready(function() {
		chart = new Highcharts.Chart({
		chart: {
		renderTo: '$balise',
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false
	},
	title: {
	text: \"$titre\"
	},
	subtitle: {
	text: \"$sous_titre\"
	},
	tooltip: {
	formatter: function() {
	return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
	}
	},
	plotOptions: {
	pie: {
	allowPointSelect: true,
	cursor: 'pointer',
	dataLabels: {
	enabled: true,
	color: '#000000',
	connectorColor: '#000000',
	formatter: function() {
	return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
	}
	},showInLegend: true
	}
		
	},
	credits: {
	enabled: false
	},
	series: [{
	type: 'pie',
	name: 'Browser share',
	data: [$series]
	}]
	});
	});

	});</script>";

	} // ...............  Fin de getPieAnalyserAnnee ...............

	/**
	 * Cette fonction affiche le code xml du Grid
	 * @return string
	 */
	public function getGridAnalyserAnnee(){

		$page = $_GET['page'];
		$limit = $_GET['rows'];
		$sidx = $_GET['sidx'];
		$sord = $_GET['sord'];

		if(!$sidx) $sidx =1;

		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";

		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";

		if(!empty($_GET['param']) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);

			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
			else $parametres3="null";

			$colonnesBDD=array("rp.idSource","election.tour",$parametres3);

			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre,".substr($nomLieu,0,-1)." as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

				for($i=0;$i<sizeof($params);$i++) {
					if($v++) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
				$theYear="";
				foreach ($listeAnnees as $lAnnee)
				{
					if ($theYear=="") $theYear.=" AND ( YEAR(dateElection)='".$lAnnee."'";
					else $theYear.= " OR YEAR(dateElection)='".$lAnnee."'";
				}
				$requete.=$theYear.")";

				$requete.=" AND rp.idCandidature=".$leCandidat." ORDER BY dateElection ASC";

				$tableauResultats[]=$this->db->query($requete)->result();
			}
		}


		$totalRows=sizeof($listeAnnees)*sizeof($listeCandidats);

		if( $totalRows > 0 && $limit > 0) {
			$total_pages = ceil($totalRows/$limit);
		}
		else {
			$total_pages = 0;
		}

		if ($page > $total_pages) $page=$total_pages;

		$start = $limit*$page - $limit;

		if($start <0) $start = 0;
			
		header("Content-type: text/xml;charset=utf-8");

		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .=  "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			foreach ($tableauResultats[$j] as $row) {
				$s .= "<row id='". $row->idCandidature ."'>";
				$s .= "<cell>". $row->nomCandidat ."</cell>";
				$s .= "<cell>". $row->lieuDeVote ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";

		echo $s;
	} // ...............  Fin de getGrid() ...............

	
	public function exportResultatsToCSV(){
		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";
	
		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
	
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;
	
		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
	
	
		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
	
		if(!empty($_GET['param']) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
	
			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
			else $parametres3="null";
	
			$colonnesBDD=array("rp.idSource","election.tour",$parametres3);
	
			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre,".substr($nomLieu,0,-1)." as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
						$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
						LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
						if ($niveau=="reg" OR $niveau=="pays")
						$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
						if ($niveau=="pays")
						$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
						for($i=0;$i<sizeof($params);$i++) {
						if($v++) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
						else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
						}
						$theYear="";
						foreach ($listeAnnees as $lAnnee)
						{
						if ($theYear=="") $theYear.=" AND ( YEAR(dateElection)='".$lAnnee."'";
						else $theYear.= " OR YEAR(dateElection)='".$lAnnee."'";
			}
			$requete.=$theYear.")";
	
			$requete.=" AND rp.idCandidature=".$leCandidat." ORDER BY dateElection ASC";
	
			$tableauResultats[]=$this->db->query($requete)->result();
	}
	}
	
	
	header("Content-type: text/csv;charset=utf-8");
	header('Content-disposition: attachment;filename=SIGeGIS - Export.csv');
	$s="Nom du candidat;Lieu de Vote;Année;Voix\r\n";
	for( $j=0;$j<sizeof($tableauResultats);$j++ ){
		foreach ($tableauResultats[$j] as $row) {
			$s .= $row->nomCandidat .";";
			$s .= $row->lieuDeVote .";";
			$s .= $row->annee .";";
			$s .= $row->nbVoix ."\r\n";
		}
	}
	
	echo $s;
	} // ...............  Fin de exportToCsv() ...............
	
	
	//------------------------------------------------------------------
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getBarAnalyserLocalite($balise){

		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";

		if(!empty($_GET['param']) AND !empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);

			$v=0;

			if ($niveau=="cen") $parametres3="centre.nomCentre";
			elseif ($niveau=="dep") $parametres3="departement.nomDepartement";
			elseif ($niveau=="reg") $parametres3="region.nomRegion";
			elseif ($niveau=="pays") $parametres3="pays.nomPays";
			else $parametres3="null";

			$colonnesBDD=array("rp.idSource","election.tour","YEAR(election.dateElection)","election.typeElection");

			foreach ($listeCandidats as $leCandidat){

				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

				for($i=0;$i<sizeof($params);$i++) {
					if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}

				$theYear="";
				foreach ($listeLocalites as $laLocalite){
					if ($theYear=="") $theYear.=" AND ( $parametres3='".$laLocalite."'";
					else $theYear.= " OR $parametres3='".$laLocalite."'";
				}
				$requete.=$theYear.")";
				$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, $parametres3 ORDER BY rp.idCandidature";

				$resultats=$this->db->query($requete)->result();

				$i=0;$j=0;

				$ordonnee="";

				foreach ($resultats as $resultat){
					if (!($j++)) $ordonnee.=$resultat->nbVoix;
					else $ordonnee.=",$resultat->nbVoix";
				}

				$tableauResultats[]="{name:'$resultat->nomCandidat', data:[".$ordonnee."]}";
			}
			
			$a=explode(",", $_GET["listeLocalites"]);
			$in=0;
			foreach ($a as $s)		if(!$in++) $abscisse.="'".$s."'"; else $abscisse.=",'".$s."'";
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Niveau d'agrégation des données";
		if ($niveau=="cen")
		{
			$sous_titre="Par centre";
		}
		elseif ($niveau=="dep")
		{
			$sous_titre="Par département";
		}
		elseif($niveau=="reg")
		{
			$sous_titre="Par région";
		}
		elseif($niveau=="pays")
		{
			$sous_titre="Par pays";
		}
		else  $titre_niveau.="Global";

		$titre=($balise=="chartdiv1")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";


		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			if ($series=="") $series.=$resultats=$tableauResultats[$j];
			else $series.=",".$resultats=$tableauResultats[$j];
		}


		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//

		return "<script type='text/javascript'>
		$(function () {
		var chart;
		$(document).ready(function() {
		chart = new Highcharts.Chart({
		chart: {
		renderTo: '$balise',
		type: 'column'
	},
	title: {
	text: \"$titre\"
	},
	subtitle: {
	text: \"$sous_titre\"
	},
	xAxis: {
	categories: [$abscisse],

	labels: {
	rotation: -40,
	align: 'right',
	style: {
	width:20,
	fontSize: '12px',
	fontFamily: 'Verdana, sans-serif'
	}
	}
	},
	yAxis: {
	min: 0,
	title: {
	text: 'NbVoix ($unite)'
	}
	},
	exporting: {
	//enabled: false
		url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
	},
	legend: {
	layout: 'vertical',
	backgroundColor: '#FFFFFF',
	align: 'right',
	verticalAlign: 'top',
	floating: true,
	shadow: true
	},
	tooltip: {
	formatter: function() {
	return  this.y;
	}
	},

	plotOptions: {
	column: {
	pointPadding: 0.2,
	borderWidth: 0,
	dataLabels: {
	enabled: true
	}
	}
	},
	credits: {
	enabled: false
	},
	series:[$series]

	});
	});
	});
	</script>";
	}// ...............  Fin de getGrid() ...............


	/**
	 * Cette fonction retourne le code JavaScript du Pie chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getPieAnalyserLocalite($balise){
		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";

		if(!empty($_GET['param']) AND !empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);

			$v=0;

			if ($niveau=="cen") $parametres3="centre.nomCentre";
			elseif ($niveau=="dep") $parametres3="departement.nomDepartement";
			elseif ($niveau=="reg") $parametres3="region.nomRegion";
			elseif ($niveau=="pays") $parametres3="pays.nomPays";
			else $parametres3="null";

			$colonnesBDD=array("rp.idSource","election.tour","YEAR(election.dateElection)","election.typeElection");

			foreach ($listeCandidats as $leCandidat){

				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

				for($i=0;$i<sizeof($params);$i++) {
					if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}

				$theYear="";
				foreach ($listeLocalites as $laLocalite){
					if ($theYear=="") $theYear.=" AND ( $parametres3='".$laLocalite."'";
					else $theYear.= " OR $parametres3='".$laLocalite."'";
				}
				$requete.=$theYear.")";
				$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, $parametres3 ORDER BY rp.idCandidature";

				$resultats=$this->db->query($requete)->result();

				$i=0;$j=0;

				$ordonnee="";

				foreach ($resultats as $resultat){
					if (!($j++)) $ordonnee.=$resultat->nbVoix;
					else $ordonnee.=",$resultat->nbVoix";
				}

				$tableauResultats[]="{name:'$resultat->nomCandidat', data:[".$ordonnee."]}";
			}
			$a=explode(",", $_GET["listeLocalites"]);
			$in=0;
			foreach ($a as $s)		if(!$in++) $abscisse.="'".$s."'"; else $abscisse.=",'".$s."'";
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Niveau d'agrégation des données";
		if ($niveau=="cen")
		{
			$sous_titre="Par centre";
		}
		elseif ($niveau=="dep")
		{
			$sous_titre="Par département";
		}
		elseif($niveau=="reg")
		{
			$sous_titre="Par région";
		}
		elseif($niveau=="pays")
		{
			$sous_titre="Par pays";
		}
		else  $titre_niveau.="Global";

		$titre=($balise=="chartdiv2")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";



		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			if ($series=="") $series.=$resultats=$tableauResultats[$j];
			else $series.=",".$resultats=$tableauResultats[$j];
		}
		return "<script>$(function () {
		var chart;
		$(document).ready(function() {
		chart = new Highcharts.Chart({
		chart: {
		renderTo: '$balise',
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false
	},
	title: {
	text: \"$titre\"
	},
	subtitle: {
	text: \"$sous_titre\"
	},
	tooltip: {
	formatter: function() {
	return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
	}
	},
	plotOptions: {
	pie: {
	allowPointSelect: true,
	cursor: 'pointer',
	dataLabels: {
	enabled: true,
	color: '#000000',
	connectorColor: '#000000',
	formatter: function() {
	return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
	}
	},showInLegend: true
	}

	},
	credits: {
	enabled: false
	},
	series: [{
	type: 'pie',
	name: 'Browser share',
	data: [$series]
	}]
	});
	});

	});</script>";

	}// ...............  Fin de getPieAnalyserLocalite() ...............

	/**
	 * Cette fonction affiche le code xml du Grid
	 * @return string
	 */
	public function getGridAnalyserLocalite(){

		$page = $_GET['page'];
		$limit = $_GET['rows'];
		$sidx = $_GET['sidx'];
		$sord = $_GET['sord'];

		if(!$sidx) $sidx =1;

		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;		

		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		

		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";

		if(!empty($_GET['param']) AND !empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);

			$v=0;

			if ($niveau=="cen") $parametres3="centre.nomCentre";
			elseif ($niveau=="dep") $parametres3="departement.nomDepartement";
			elseif ($niveau=="reg") $parametres3="region.nomRegion";
			elseif ($niveau=="pays") $parametres3="pays.nomPays";
			else $parametres3="null";

			$colonnesBDD=array("rp.idSource","election.tour","YEAR(election.dateElection)","election.typeElection");

			foreach ($listeCandidats as $leCandidat){

				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre,$parametres3 as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

				for($i=0;$i<sizeof($params);$i++) {
					if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}

				$theYear="";
				foreach ($listeLocalites as $laLocalite){
					if ($theYear=="") $theYear.=" AND ( $parametres3='".$laLocalite."'";
					else $theYear.= " OR $parametres3='".$laLocalite."'";
				}
				$requete.=$theYear.")";
				$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, $parametres3 ORDER BY rp.idCandidature";

				$tableauResultats[]=$this->db->query($requete)->result();
			}
		}

		$totalRows=sizeof($listeLocalites)*sizeof($listeCandidats);

		if( $totalRows > 0 && $limit > 0) {
			$total_pages = ceil($totalRows/$limit);
		}
		else {
			$total_pages = 0;
		}

		if ($page > $total_pages) $page=$total_pages;

		$start = $limit*$page - $limit;

		if($start <0) $start = 0;

		header("Content-type: text/xml;charset=utf-8");

		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .=  "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
		for( $j=0;$j<sizeof($tableauResultats);$j++ ){
			foreach ($tableauResultats[$j] as $row) {
				$s .= "<row id='". $row->idCandidature ."'>";
				$s .= "<cell>". $row->nomCandidat ."</cell>";
				$s .= "<cell>". $row->lieuDeVote ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";
		echo $s;
	} // ...............  Fin de tableauLocalite() ...............

	
	public function exportToCSVLocalite(){
		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";
	
	
		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
	
		if ($typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($typeElection=="legislative") $titreElection="législative";
		elseif ($typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$typeElection;
	
		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
	
	
		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
	
		if(!empty($_GET['param']) AND !empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
	
			$v=0;
	
			if ($niveau=="cen") $parametres3="centre.nomCentre";
			elseif ($niveau=="dep") $parametres3="departement.nomDepartement";
			elseif ($niveau=="reg") $parametres3="region.nomRegion";
			elseif ($niveau=="pays") $parametres3="pays.nomPays";
			else $parametres3="null";
	
			$colonnesBDD=array("rp.idSource","election.tour","YEAR(election.dateElection)","election.typeElection");
	
			foreach ($listeCandidats as $leCandidat){
	
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, rp.idCentre,$parametres3 as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
						$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
						LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
						if ($niveau=="reg" OR $niveau=="pays")
						$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
						if ($niveau=="pays")
						$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
						for($i=0;$i<sizeof($params);$i++) {
						if($v++) {
						$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				}
				else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
	
				$theYear="";
				foreach ($listeLocalites as $laLocalite){
				if ($theYear=="") $theYear.=" AND ( $parametres3='".$laLocalite."'";
				else $theYear.= " OR $parametres3='".$laLocalite."'";
			}
			$requete.=$theYear.")";
			$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, $parametres3 ORDER BY rp.idCandidature";
	
			$tableauResultats[]=$this->db->query($requete)->result();
	}
	}
	
				$totalRows=sizeof($listeLocalites)*sizeof($listeCandidats);
	
				if( $totalRows > 0 && $limit > 0) {
				$total_pages = ceil($totalRows/$limit);
	}
				else {
				$total_pages = 0;
	}
	
	if ($page > $total_pages) $page=$total_pages;
	
	$start = $limit*$page - $limit;
	
	if($start <0) $start = 0;
	
	header("Content-type: text/csv;charset=utf-8");
	header('Content-disposition: attachment;filename=SIGeGIS - Export.csv');
	$s="Nom du candidat;Lieu de Vote;Année;Voix\r\n";

	for( $j=0;$j<sizeof($tableauResultats);$j++ ){
		foreach ($tableauResultats[$j] as $row) {
			$s .= $row->nomCandidat .";";
			$s .= $row->lieuDeVote .";";
			$s .= $row->annee .";";
			$s .= $row->nbVoix ."\r\n";
		}
	}
	echo $s;
	} // ...............  Fin de tableauLocalite() ...............
	
	
	function test(){
		$candidats="";$seriesGlob="";$series="";$localites="";
		echo "<script>
			var chart;
			$(document).ready(function() {
		
				var colors = Highcharts.getOptions().colors,
				categories = [$candidats],
				name = 'Browser brands',
				data = [{
					y: $seriesGlob,
					color: colors[0],
					drilldown: {
						name: 'MSIE versions',
						categories: [$localites],
						data: [$series],
						color: colors[0]
					}
				}];
		
		
				// Build the data arrays
				var browserData = [];
				var versionsData = [];
				for (var i = 0; i < data.length; i++) {
		
					// add browser data
					browserData.push({
						name: categories[i],
						y: data[i].y,
						color: data[i].color
					});
		
					// add version data
					for (var j = 0; j < data[i].drilldown.data.length; j++) {
						var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
						versionsData.push({
							name: data[i].drilldown.categories[j],
							y: data[i].drilldown.data[j],
							color: Highcharts.Color(data[i].color).brighten(brightness).get()
						});
					}
				}
		
				// Create the chart
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						type: 'pie'
					},
					title: {
						text: 'Browser market share, April, 2011'
					},
					yAxis: {
						title: {
							text: 'Total percent market share'
						}
					},
					plotOptions: {
						pie: {
							shadow: false
						}
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
						}
					},
					series: [{
						name: 'Browsers',
						data: browserData,
						size: '60%',
						dataLabels: {
							formatter: function() {
								//return this.y > 5 ? this.point.name : null;
							},
							color: 'white',
							distance: -30
						}
					}, {
						name: 'Versions',
						data: versionsData,
						innerSize: '60%',
						dataLabels: {
							formatter: function() {
								// display only if larger than 1
								//return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
							}
						}
					}]
				});
			});</script>";		
	}
}