<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Amadou SOW && Abdou Khadre GUEYE DESS | 2ITIC 2011-2012
 * Description: Ce modèle gère l'exportation des données ainsi que leur affichage pour la partie dédié à la visualisation des données 
 *
 */
class Main_model extends CI_Model{
private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	
	public function getBarVisualiser($balise){		

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

		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";

		$params=explode(",",$parametres);
		$v=0;

		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource, SUM( nbVoix ) as nbVoix
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


		if ($niveau=="cen") $parametres3="centre.idCentre";
		elseif ($niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($niveau=="reg") $parametres3="region.idRegion";
		elseif ($niveau=="pays") $parametres3="pays.idPays";
		else $parametres3="null";

		$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);

		for($i=0;$i<sizeof($params);$i++) {
			if($v++){
				if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}

		$requete.=" GROUP BY idCandidature";

		$resultats=$this->db->query($requete)->result();

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Election ";
		
		$titre_niveau.=" $titreElection ".$resultats[0]->annee.": résultats ";
		
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
		$titre=($balise=="chartdiv1")?$titre_niveau:"Erreur sur l'emplacement du graphique !";

		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		$i=0;$j=0;
		$abscisse="";$ordonnee="";

		foreach ($resultats as $resultat){
			if (!($i++)) $abscisse.="'$resultat->nomCandidat'";
			else $abscisse.=",'$resultat->nomCandidat'";
			if (!($j++)) $ordonnee.=$resultat->nbVoix;
			else $ordonnee.=",$resultat->nbVoix";
		}

		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//

		return "<script type='text/javascript'>
		$(function () {
		
		$(document).ready(function() {
		chart1 = new Highcharts.Chart({
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
			url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
		},
		legend: {
			enabled:false			
		},
		tooltip: {
			formatter: function() {
				return  this.x +': '+ this.y;
			}
		},
	
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				colorByPoint: true,
				dataLabels: {
					enabled: true
				}
			}
		},
		credits: {
			enabled: false
		},
		series: [{name:'Résultats',data:[$ordonnee]}]
		});
		});	
		});
		</script>";
	} // ...............  Fin de getBarVisualiser() ...............
	
	/**
	 * Cette fonction retourne le code JavaScript du Pie chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getPieVisualiser($balise){
		
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

		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";

		$params=explode(",",$parametres);
		$v=0;

		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource, SUM( nbVoix ) as nbVoix
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


		if ($niveau=="cen") $parametres3="centre.idCentre";
		elseif ($niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($niveau=="reg") $parametres3="region.idRegion";
		elseif ($niveau=="pays") $parametres3="pays.idPays";
		else $parametres3="null";

		$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);

		for($i=0;$i<sizeof($params);$i++) {
			if($v++){
				if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}

		$requete.=" GROUP BY idCandidature";

		$resultats=$this->db->query($requete)->result();

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Election ";
		
		$titre_niveau.=" $titreElection ".$resultats[0]->annee.": résultats ";
		
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
		$titre=($balise=="chartdiv2")?$titre_niveau:"Erreur sur l'emplacement du graphique !";

		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		$line="";
		$i=0;

		foreach ($resultats as $resultat)
			if (!($i++)) $line.="{name: '$resultat->nomCandidat',y: $resultat->nbVoix,sliced: true,selected: true}";
		else $line.=",['$resultat->nomCandidat',$resultat->nbVoix]";

		return "<script>$(function () {
		
		$(document).ready(function() {
		chart2 = new Highcharts.Chart({
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
			},
			showInLegend: true
		}
			
		},
		exporting: {
			url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
		},
		credits: {
		enabled: false
		},
		series: [{
		type: 'pie',
		name: 'Browser share',
		data: [$line]
		}]
		});
		});
	
		});</script>";

	} // ...............  Fin de getPieVisualiser() ...............

	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridVisualiser(){

		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
		
		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
		
		
		$page = $_GET['page'];
		$limit = $_GET['rows'];
		$sidx = $_GET['sidx'];
		$sord = $_GET['sord'];

		if(!$sidx) $sidx =1;


		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
			$parametres3=null;
				
			$requete="SELECT rp.idCandidature, nomCandidat,nomSource, SUM( nbVoix ) as nbVoix
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
				
			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
				

			$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
				
			for($i=0;$i<sizeof($params);$i++) {
				if( $colonnesBDD[$i] ){
					if($v++)$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
			}
				
			$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, nomCandidat,nomSource
			FROM resultatspresidentielles rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre WHERE YEAR(election.dateElection)={$params[1]} AND election.typeElection='$typeElection'";
				
			$requeteCount.=" GROUP BY rp.idCandidature) as S";
				
			$count = $this->db->query($requeteCount)->result();
				
			$totalRows=$count[0]->total;
				
			if( $totalRows > 0 && $limit > 0) {
				$total_pages = ceil($totalRows/$limit);
			}
			else {
				$total_pages = 0;
			}
				
			if ($page > $total_pages) $page=$total_pages;
				
			$start = $limit*$page - $limit;
				
			if($start <0) $start = 0;
				
			$requete.=" GROUP BY idCandidature ORDER BY $sidx $sord LIMIT $start,$limit";
				
			$resultats=$this->db->query($requete)->result();
		}

			
		header("Content-type: text/xml;charset=utf-8");

		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";

		foreach ($resultats as $row) {
			$s .= "<row id='". $row->idCandidature ."'>";
			$s .= "<cell>". $row->nomCandidat ."</cell>";
			$s .= "<cell>". $row->nbVoix ."</cell>";
			$s .= "</row>";
		}
		$s .= "</rows>";

		echo $s;
	} // ...............  Fin de getGrid() ...............
	
	public function exportResultatsToCSV(){
		
		$sord = $_GET['sord'];
	
		if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
		else return;
	
		if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
		else $niveau=null;
			
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
			$parametres3=null;
	
			$requete="SELECT rp.idCandidature, nomCandidat,nomSource, SUM( nbVoix ) as nbVoix
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
	
							if ($niveau=="cen") $parametres3="centre.idCentre";
							elseif ($niveau=="dep") $parametres3="departement.idDepartement";
							elseif ($niveau=="reg") $parametres3="region.idRegion";
							elseif ($niveau=="pays") $parametres3="pays.idPays";
	
	
									$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
	
									for($i=0;$i<sizeof($params);$i++) {
											if( $colonnesBDD[$i] ){
										if($v++)$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
										else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
		}
	
				$requete.=" GROUP BY idCandidature ORDER BY nbVoix $sord";
	
				$resultats=$this->db->query($requete)->result();
	}
	
	
	header("Content-type: text/csv;charset=utf-8");
	header('Content-disposition: attachment;filename=SIGeGIS - Export.csv');
	$s="Nom du candidat;Voix\r\n";
	foreach ($resultats as $row) {
	$s .= $row->nomCandidat .";";
	$s .= $row->nbVoix;
	$s .= "\r\n";
	}
	
	echo $s;
	} // ...............  Fin de getGrid() ...............	
	
	public function build_menu(){
		return "<div id='menu-css'>
		<ul>
		<li><a class='actif' href='".site_url()."'>Accueil</a></li>
		<li><a href='".site_url("main_controller/administration")."'>Administration</a></li>
		<li><a href='".site_url("main_controller/visualiser?type=$typeElection&amp;niveau=globaux").">Résultats globaux</a></li>
		<li><a href='".site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=reg").">Résultats régionaux</a></li>
		<li><a href='".site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=dep").">Résultats départementaux</a></li>
		<li><a href='".site_url("main_controller/visualiser?type=".$typeElection."&amp;niveau=cen").">Résultats au niveau des centres</a></li>
		<li><a href='".site_url("main_controller/participation?type=".$typeElection).">Statistiques</a></li>
		</ul>
		</div>";
	}


	public function getBarParticipation($balise){
	
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
		else $nomLieu="'Participation au niveau  national' as nomLieu,";
	
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";
	
		$params=explode(",",$parametres);
		$source="";
		$v=0;
	
		$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection		
		LEFT JOIN source ON rp.idSource = source.idSource	
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
	
	
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
	
					if ($niveau=="cen") $parametres3="centre.idCentre";
					elseif ($niveau=="dep") $parametres3="departement.idDepartement";
					elseif ($niveau=="reg") $parametres3="region.idRegion";
					elseif ($niveau=="pays") $parametres3="pays.idPays";
					else $parametres3="null";
	
					$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
	
					for($i=0;$i<sizeof($params);$i++) {
							if($v++){
							if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
						}
						else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";						
					}
	
	
		$resultats=$this->db->query($requete)->result();

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		
		$titre_niveau="Election ";
		
		$titre_niveau.=" $titreElection ".$resultats[0]->annee." | Source: $source";
		
		if ($niveau=="cen") {
			/*$titre_niveau.=": résultats par centre ";*/$sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			/*$titre_niveau.=": résultats départementaux ";*/$sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			/*$titre_niveau.=": résultats régionaux ";*/$sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			/*$titre_niveau.=": résultats par pays ";*/$sous_titre="Pays: ";
		}
		//else  $titre_niveau.=": résultats globaux ";				
		
	
	
		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv1")?$titre_niveau:"Erreur sur l'emplacement du graphique !";
	
		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
	
		$i=0;$j=0;
		$abscisse="";$ordonnee="";
	
		foreach ($resultats as $resultat){
			$ordonnee=$resultat->inscrits.",".$resultat->votants.",".$resultat->nuls.",".$resultat->exprimes;
			$source=$resultat->nomSource;
		}
	
		/*if(!empty($_GET['unite'])){
		if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";*/
	
		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
	
		return "<script type='text/javascript'>
		$(function () {
	
		$(document).ready(function() {
		chart1 = new Highcharts.Chart({
		chart: {
		renderTo: '$balise',
		type: 'column'
	},
	title: {
		text: \"$titre $source\"
	},
	subtitle: {
		text: \"$sous_titre\"
	},
	xAxis: {
		categories: ['Inscrits','Votants','Bulletins nuls','Suffrages exprimés'],
		labels: {		
			style: {		
				fontSize: '12px',
				fontWeight: 'bold',
				fontFamily: 'Verdana, sans-serif'
			}
		}
	},
	yAxis: {
	min: 0,
	title: {
		text: 'Nombre'
	}
	},
	exporting: {
		url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
	},
	legend: {
		enabled:false
	},
	tooltip: {
		formatter: function() {
			return  this.x +': '+ this.y;
		}
	},
	
	plotOptions: {
		column: {
			pointPadding: 0.2,
			borderWidth: 0,
			colorByPoint: true,
			dataLabels: {
				enabled: true
			}
		}
	},
	credits: {
		enabled: false
	},
		series: [{name:'Résultats',data:[$ordonnee]}]
		});
		});
	
		});
		</script>";
		} // ...............  Fin de getBarVisualiser() ...............
	
		/**
		* Cette fonction retourne le code JavaScript du Pie chart
		* @return string
		* @param string $balise Le nom du conteneur Html
		*/
		public function getPieParticipation($balise){
	
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
		else $nomLieu="'Participation au niveau  national' as nomLieu,";
	
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";
	
		$params=explode(",",$parametres);
		$v=0;
	
		$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection		
		LEFT JOIN source ON rp.idSource = source.idSource	
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
	
	
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
	
					if ($niveau=="cen") $parametres3="centre.idCentre";
					elseif ($niveau=="dep") $parametres3="departement.idDepartement";
					elseif ($niveau=="reg") $parametres3="region.idRegion";
					elseif ($niveau=="pays") $parametres3="pays.idPays";
					else $parametres3="null";
	
					$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
	
					for($i=0;$i<sizeof($params);$i++) {
							if($v++){
							if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
							}
							else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
					}
	
		$resultats=$this->db->query($requete)->result();

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//		
		
		$titre_niveau="Election $titreElection ".$resultats[0]->annee;
		$titre_niveau.=" participation ";
		
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
		else  $titre_niveau.="au niveau national ";		
	
		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv2")?$titre_niveau:"Erreur sur l'emplacement du graphique !";
	
		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
	
		$i=0;$j=0;
	
		foreach ($resultats as $resultat){				
			$line="{name: 'Votants',y:$resultat->votants ,sliced: true,selected: true}";			
			$line.=",{name: 'Abstention',y:$resultat->abstention}";
			$line2="{name: 'Exprimés',y:$resultat->exprimes ,sliced: true,selected: true}";			
			$line2.=",{name: 'Nuls',y:$resultat->nuls}";			
		}
	
		if(!empty($_GET['unite'])){
		if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
	} else  $unite="En valeurs absolues";
		return "<script>$(function () {

		$(document).ready(function() {
		chart2 = new Highcharts.Chart({
			chart: {
				renderTo: '$balise',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title: {
				text: 'Participation',
				style:{
					width:'300px'
				}
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
							return this.percentage.toFixed(2) +' %';
						}
					},
					showInLegend: true
				}
			},
			exporting: {
				url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
			},
			credits: {
				enabled: false
			},
			series: [{
				type: 'pie',
				name: 'Browser share',
				data: [$line]
			}]
		});
		
		
		chart3 = new Highcharts.Chart({
			chart: {
				renderTo: 'chartdiv3',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title: {
				text: 'Participation',
				style:{
					width:'300px'
				}
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
					},
					showInLegend: true
				}			
			},
			exporting: {
				url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
			},
			credits: {
				enabled: false
			},
			series: [{
				type: 'pie',
				name: 'Browser share',
				data: [$line2]
			}]
			});
			});		
		});
		</script>";	
	} // ...............  Fin de getPieVisualiser() ...............
	
	/**
	* Cette fonction affiche le code xml du Grid
	* @return string
	* @param string $balise Le nom du conteneur Html
	*/
	public function getGridParticipation(){
	
	if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
	else return;
	
	if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
	else $niveau=null;
	
	
	$page = $_GET['page'];
	$limit = $_GET['rows'];
	$sidx = $_GET['sidx'];
	$sord = $_GET['sord'];
	
	if(!$sidx) $sidx =1;
	
	if ($niveau=="cen") $nomLieu="nomCentre as nomLieu,";
	elseif ($niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
	elseif ($niveau=="reg") $nomLieu="nomRegion as nomLieu,";
	elseif ($niveau=="pays") $nomLieu="nomPays as nomLieu,";
	else $nomLieu="'Participation au niveau  national' as nomLieu,";
	
	if(!empty($_GET['param'])){
	$parametres=$_GET['param'];
	$params=explode(",",$parametres);
	$v=0;
	$parametres3=null;
	
	$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
	FROM {$this->tablesParticipation[$typeElection]} rp
	LEFT JOIN election ON rp.idElection = election.idElection		
	LEFT JOIN source ON rp.idSource = source.idSource	
	LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
	if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
	$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
	LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
	if ($niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
				if ($niveau=="cen") $parametres3="centre.idCentre";
				elseif ($niveau=="dep") $parametres3="departement.idDepartement";
				elseif ($niveau=="reg") $parametres3="region.idRegion";
				elseif ($niveau=="pays") $parametres3="pays.idPays";
				else $parametres3="null";


				$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);

				for($i=0;$i<sizeof($params);$i++) {
					if($v++){
						if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					}
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
	
	$totalRows=1;
	
	if( $totalRows > 0 && $limit > 0) {
	$total_pages = ceil($totalRows/$limit);
	}
	else {
	$total_pages = 0;
	}
	
	if ($page > $total_pages) $page=$total_pages;
	
	$start = $limit*$page - $limit;
	
	if($start <0) $start = 0;
	
	$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
	$resultats=$this->db->query($requete)->result();
	}
	
	
	header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
		$s .= "<row id='". $row->idElection ."'>";
		$s .= "<cell>". $row->nomLieu ."</cell>";
		$s .= "<cell>". $row->inscrits ."</cell>";
		$s .= "<cell>". $row->votants ."</cell>";
		$s .= "<cell>". $row->nuls ."</cell>";
		$s .= "<cell>". $row->exprimes ."</cell>";
		$s .= "<cell>". $row->abstention ."</cell>";
		$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
		} // ...............  Fin de getGrid() ...............

		public function getPoidsElectoralRegions($balise){
			if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
			else return;
			if (!empty($_GET['annee']) AND !empty($_GET['tour'])) {$annee=$_GET['annee'];$tour=$_GET['tour'];} else return;
			
			$requete="SELECT nomRegion, SUM( nbInscrits ) as inscrits
			FROM {$this->tablesParticipation[$typeElection]} rp
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre 
			LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement 
			LEFT JOIN region ON departement.idRegion = region.idRegion 
			WHERE YEAR(dateElection)=$annee AND nomRegion<>'ETRANGER' AND election.tour='$tour' 
			GROUP BY region.idRegion ORDER BY nomRegion";
		
			$resultats=$this->db->query($requete)->result();
		
			// ----------------------------------------	//
			//			TITRES DES DIAGRAMMES			//
			// ----------------------------------------	//
			$titre_niveau="Election ";
			if ($typeElection=="presidentielle") $titreElection="présidentielle";
			elseif ($typeElection=="legislative") $titreElection="législative";
			elseif ($typeElection=="regionale") $titreElection="régionale";
			else $titreElection=$typeElection;
			$titre_niveau.=" $titreElection ".$annee;
			$sous_titre="Poids électoral des régions"; 		
			$titre=($balise=="chartdiv4")?$titre_niveau:"Erreur sur l'emplacement du graphique !";
		
			// ----------------------------------------	//
			//			COLLECTE DES DONNEES			//
			// ----------------------------------------	//
		
			$line="";
			$i=0;
		
			foreach ($resultats as $resultat)
					if (!($i++)) $line.="{name: '$resultat->nomRegion',y: $resultat->inscrits,sliced: true,selected: true}";
					else $line.=",['$resultat->nomRegion',$resultat->inscrits]";
		
					return "<script>$(function () {
		
					$(document).ready(function() {
						chart4 = new Highcharts.Chart({
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
								text: '$sous_titre'
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
									},
									showInLegend: true
								}					
							},
							exporting: {
								url:'http://www.sigegis.ugb-edu.com/assets/js/highcharts/exporting-server/index.php'
							},
							credits: {
								enabled: false
							},
							series: [{
								type: 'pie',
								name: 'Browser share',
								data: [$line]
							}]
						});
					});					
					});</script>";
		
		} // ...............  Fin de getPoidsElectoralRegions() ...............
		
		
		
		public function exportStatisticsToCSV(){
		
			if(!empty($_GET["typeElection"])) $typeElection=$_GET["typeElection"];
			else return;
		
			if(!empty($_GET["niveau"]))	$niveau=$_GET["niveau"];
			else $niveau=null;

			if ($niveau=="cen") $nomLieu="nomCentre as nomLieu,";
			elseif ($niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
			elseif ($niveau=="reg") $nomLieu="nomRegion as nomLieu,";
			elseif ($niveau=="pays") $nomLieu="nomPays as nomLieu,";
			else $nomLieu="'Participation au niveau  national' as nomLieu,";
		
			if(!empty($_GET['param'])){
				$parametres=$_GET['param'];
				$params=explode(",",$parametres);
				$v=0;
				$parametres3=null;
		
				$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
				FROM {$this->tablesParticipation[$typeElection]} rp
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
					if ($niveau=="reg" OR $niveau=="pays")
							$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
							if ($niveau=="pays")
									$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
									if ($niveau=="cen") $parametres3="centre.idCentre";
									elseif ($niveau=="dep") $parametres3="departement.idDepartement";
									elseif ($niveau=="reg") $parametres3="region.idRegion";
									elseif ($niveau=="pays") $parametres3="pays.idPays";
									else $parametres3="null";
		
		
									$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
		
									for($i=0;$i<sizeof($params);$i++) {
									if($v++){
									if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
									}					
		
									$resultats=$this->db->query($requete)->result();
		}
		
		
		header("Content-type: text/csv;charset=utf-8");
		header('Content-disposition: attachment;filename=SIGeGIS - Statistiques.csv');
		
		$s = "Lieu de vote;Inscrits;Votants;Nuls;Suffrages exprimes;Abstention\r\n";
		
		foreach ($resultats as $row) {		
		$s .= $row->nomLieu .";";
		$s .= $row->inscrits .";";
		$s .= $row->votants .";";
		$s .= $row->nuls .";";
		$s .= $row->exprimes .";";
		$s .= $row->abstention ."\r\n";
		}
		
		echo $s;
		} // ...............  Fin de getGrid() ...............
}