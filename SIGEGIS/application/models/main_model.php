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
private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
private $typeElection=null;
private $niveau=null;
private $titreElection="";

public function __construct(){
	if(!empty($_GET["typeElection"])) {
		$this->typeElection=$_GET["typeElection"];	
		if ($this->typeElection=="presidentielle") $this->titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $this->titreElection="législative";
		elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$this->typeElection;
	}
	if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];
	else $this->niveau=null;	
}
	/**
	 * Cette fonction retourne les données pour l'histogramme
	 * @return JSON object
	 */	
	public function getBarVisualiser(){						
		
		if ($this->niveau=="cen") $nomLieu="nomCentre,";
		elseif ($this->niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($this->niveau=="reg") $nomLieu="nomRegion,";
		elseif ($this->niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
		
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";
		
		$params=explode(",",$parametres);
		$v=0;
		
		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource,partiActuel, SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$this->typeElection]} rp
		LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
						
		if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($this->niveau=="pays")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		
		if ($this->niveau=="cen") $parametres3="centre.idCentre";
		elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($this->niveau=="reg") $parametres3="region.idRegion";
		elseif ($this->niveau=="pays") $parametres3="pays.idPays";
		else $parametres3="null";
		
		$colonnesBDD=array();
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
		$colonnesBDD[]=$parametres3;
		
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
	
		$titre_niveau.=" $this->titreElection ".$resultats[0]->annee.": résultats ";
		
		if ($this->niveau=="cen") {
			$titre_niveau.="par centre ";$sous_titre="Centre: ";
		}
		elseif ($this->niveau=="dep") {
			$titre_niveau.="départementaux ";$sous_titre="Département: ";
		}
		elseif($this->niveau=="reg") {
			$titre_niveau.="régionaux ";$sous_titre="Région: ";
		}
		elseif($this->niveau=="pays") {
			$titre_niveau.="par pays ";$sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";
		
		
		if ($this->niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($this->niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($this->niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($this->niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=$titre_niveau;
		
		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
		
		$i=0;$j=0;
		$abscisse=array();$ordonnee=array();

		foreach ($resultats as $resultat){
			$abscisse[]=$resultat->nomCandidat."<br /><b>".$resultat->partiActuel."</b>";
			$ordonnee[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$i++]}");			
		}
		
		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";
		
		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
		
		$rendu=array();
		$rendu["titre"]=$titre;
		$rendu["sous_titre"]=$sous_titre;
		$rendu["abscisse"]=$abscisse;
		$rendu["ordonnee"]=$ordonnee;
		$rendu["unite"]=$unite;	
		
		echo json_encode($rendu);	
		
		
		} // ...............  Fin de getBarVisualiser() ...............
	
	
	public function getPieVisualiser(){			
		
		if ($this->niveau=="cen") $nomLieu="nomCentre,";
		elseif ($this->niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($this->niveau=="reg") $nomLieu="nomRegion,";
		elseif ($this->niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";

		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";

		$params=explode(",",$parametres);
		$v=0;

		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource, SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$this->typeElection]} rp
		LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";

		if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($this->niveau=="pays")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

		if ($this->niveau=="cen") $parametres3="centre.idCentre";
		elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($this->niveau=="reg") $parametres3="region.idRegion";
		elseif ($this->niveau=="pays") $parametres3="pays.idPays";
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
		
		$titre_niveau.=" $this->titreElection ".$resultats[0]->annee.": résultats ";
		
		if ($this->niveau=="cen") {
			$titre_niveau.="par centre ";$sous_titre="Centre: ";
		}
		elseif ($this->niveau=="dep") {
			$titre_niveau.="départementaux ";$sous_titre="Département: ";
		}
		elseif($this->niveau=="reg") {
			$titre_niveau.="régionaux ";$sous_titre="Région: ";
		}
		elseif($this->niveau=="pays") {
			$titre_niveau.="par pays ";$sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";				
		


		if ($this->niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($this->niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($this->niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($this->niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=$titre_niveau;

		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//

		$pieData=array();
		$i=0;
		
		foreach ($resultats as $resultat){
			$pieData[]=array("name"=>$resultat->nomCandidat,"y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$i++]}");
		}
						
		$rendu=array();
		$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre);		
		$rendu[]=array("type"=>"pie","name"=>$titre,"data"=>$pieData);
		
		echo json_encode($rendu);

	} // ...............  Fin de getPieVisualiser() ...............
		
	
	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridVisualiser(){		
		
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
			
			if ($this->niveau=="cen") $parametres3="centre.idCentre";
			elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($this->niveau=="reg") $parametres3="region.idRegion";
			elseif ($this->niveau=="pays") $parametres3="pays.idPays";
			
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]=$parametres3;			
			
			$requeteTOTAL="SELECT SUM( nbVoix ) ";

			$joinPART=" FROM {$this->tables[$this->typeElection]} rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
			
			if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
			$joinPART.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($this->niveau=="reg" OR $this->niveau=="pays")
			$joinPART.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($this->niveau=="pays")
			$joinPART.=" LEFT JOIN pays ON region.idPays = pays.idPays";

			$requeteTOTAL.=$joinPART;
			$v=0;
			for($i=0;$i<sizeof($params);$i++) {
				if( $colonnesBDD[$i] ){
					if($v++)$wherePART.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else $wherePART=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
			}
			$requeteTOTAL.=$wherePART;
			
			$requete="SELECT rp.idCandidature, nomCandidat,nomSource, SUM( nbVoix ) as nbVoix, (100*SUM( nbVoix )/($requeteTOTAL)) as pourcentage";
			$requete.=$joinPART.$wherePART;
			

			
					
	
			$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, nomCandidat,nomSource
			FROM {$this->tables[$this->typeElection]} rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre WHERE YEAR(election.dateElection)={$params[1]} AND election.typeElection='$this->typeElection'";
		
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
	$s .= "<cell>". $row->pourcentage ."</cell>";
	$s .= "</row>";
	}
	$s .= "</rows>";
	
	echo $s;
	} // ...............  Fin de getGrid() ...............
	
	public function exportResultatsToCSV(){
	
		$sord = $_GET['sord'];
	
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
			$parametres3=null;
			
			if ($this->niveau=="cen") $parametres3="centre.idCentre";
			elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($this->niveau=="reg") $parametres3="region.idRegion";
			elseif ($this->niveau=="pays") $parametres3="pays.idPays";
			
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]=$parametres3;			
			
			$requeteTOTAL="SELECT SUM( nbVoix ) ";

			$joinPART=" FROM {$this->tables[$this->typeElection]} rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
			
			if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
			$joinPART.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($this->niveau=="reg" OR $this->niveau=="pays")
			$joinPART.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($this->niveau=="pays")
			$joinPART.=" LEFT JOIN pays ON region.idPays = pays.idPays";

			$requeteTOTAL.=$joinPART;
			$v=0;
			for($i=0;$i<sizeof($params);$i++) {
				if( $colonnesBDD[$i] ){
					if($v++)$wherePART.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else $wherePART=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
			}
			$requeteTOTAL.=$wherePART;
			
			$requete="SELECT rp.idCandidature, nomCandidat,nomSource, SUM( nbVoix ) as nbVoix, (100*SUM( nbVoix )/($requeteTOTAL)) as pourcentage";
			$requete.=$joinPART.$wherePART;
			
		$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, nomCandidat,nomSource
		FROM resultatspresidentielles rp
		LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre WHERE YEAR(election.dateElection)={$params[1]} AND election.typeElection='$typeElection'";

		$requete.=" GROUP BY idCandidature ORDER BY nbVoix $sord";

		$resultats=$this->db->query($requete)->result();
	}
	
	header("Content-type: text/csv;charset=utf-8");
	header('Content-disposition: attachment;filename=SIGeGIS - Export.csv');
	$s="Nom du candidat;Voix;% exprimes\r\n";
	foreach ($resultats as $row) {
	$s .= $row->nomCandidat .";";
	$s .= $row->nbVoix .";";
	$s .= $row->pourcentage;
	$s .= "\r\n";
	}
	
	echo $s;
	} // ...............  Fin de getGrid() ...............	
	
		public function getBarParticipation(){
	
		if ($this->niveau=="cen") $nomLieu="nomCentre,";
		elseif ($this->niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($this->niveau=="reg") $nomLieu="nomRegion,";
		elseif ($this->niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="'Participation au niveau  national' as nomLieu,";
	
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
		}
		else $parametres="1,2012,premier_tour,globaux";
	
		$params=explode(",",$parametres);
		$source="";
		$v=0;
	
		$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$this->typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection		
		LEFT JOIN source ON rp.idSource = source.idSource	
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
		if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($this->niveau=="pays")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
	
					if ($this->niveau=="cen") $parametres3="centre.idCentre";
					elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
					elseif ($this->niveau=="reg") $parametres3="region.idRegion";
					elseif ($this->niveau=="pays") $parametres3="pays.idPays";
					else $parametres3="null";
	
					$colonnesBDD=array();
					$colonnesBDD[]="rp.idSource";
					$colonnesBDD[]="YEAR(election.dateElection)";
					if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
					$colonnesBDD[]=$parametres3;
	
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
		
		if ($this->niveau=="cen") {
			/*$titre_niveau.=": résultats par centre ";*/$sous_titre="Centre: ";
		}
		elseif ($this->niveau=="dep") {
			/*$titre_niveau.=": résultats départementaux ";*/$sous_titre="Département: ";
		}
		elseif($this->niveau=="reg") {
			/*$titre_niveau.=": résultats régionaux ";*/$sous_titre="Région: ";
		}
		elseif($this->niveau=="pays") {
			/*$titre_niveau.=": résultats par pays ";*/$sous_titre="Pays: ";
		}
		//else  $titre_niveau.=": résultats globaux ";				
		
	
	
		if ($this->niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($this->niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($this->niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($this->niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=$titre_niveau;
	
		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
	
		$i=0;$j=0;
	
		foreach ($resultats as $resultat){
			$ordonnee=$resultat->inscrits.",".$resultat->votants.",".$resultat->nuls.",".$resultat->exprimes;
			$source=$resultat->nomSource;
		}
	
		$abscisse=array();$ordonnee=array();

		foreach ($resultats as $resultat){
			$source=$resultat->nomSource;
			$ordonnee[]=array("y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[0]}");
			$ordonnee[]=array("y"=>(int)$resultat->votants,"color"=>"{$this->colors[1]}");
			$ordonnee[]=array("y"=>(int)$resultat->nuls,"color"=>"{$this->colors[2]}");
			$ordonnee[]=array("y"=>(int)$resultat->exprimes,"color"=>"{$this->colors[3]}");
		}
	
		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
		$rendu=array();
		$rendu["titre"]=$titre;
		$rendu["sous_titre"]=$sous_titre;
		$rendu["abscisse"]=$abscisse;
		$rendu["ordonnee"]=$ordonnee;
		//$rendu["unite"]=$unite;
		
		echo json_encode($rendu);		
		
		} // ...............  Fin de getBarParticipation() ...............
	
		/**
		* Cette fonction retourne le code JavaScript du Pie chart
		* @return string
		* @param string $balise Le nom du conteneur Html
		*/
		
	
	/**
	* Cette fonction affiche le code xml du Grid
	* @return string
	* @param string $balise Le nom du conteneur Html
	*/
	public function getGridParticipation(){	
	
	$page = $_GET['page'];
	$limit = $_GET['rows'];
	$sidx = $_GET['sidx'];
	$sord = $_GET['sord'];
	
	if(!$sidx) $sidx =1;
	
	if ($this->niveau=="cen") $nomLieu="nomCentre as nomLieu,";
	elseif ($this->niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
	elseif ($this->niveau=="reg") $nomLieu="nomRegion as nomLieu,";
	elseif ($this->niveau=="pays") $nomLieu="nomPays as nomLieu,";
	else $nomLieu="'Participation au niveau  national' as nomLieu,";
	
	if(!empty($_GET['param'])){
	$parametres=$_GET['param'];
	$params=explode(",",$parametres);
	$v=0;
	$parametres3=null;
	
	$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
	FROM {$this->tablesParticipation[$this->typeElection]} rp
	LEFT JOIN election ON rp.idElection = election.idElection		
	LEFT JOIN source ON rp.idSource = source.idSource	
	LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
	if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
	$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
	LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
	if ($this->niveau=="reg" OR $this->niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($this->niveau=="pays")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
				if ($this->niveau=="cen") $parametres3="centre.idCentre";
				elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
				elseif ($this->niveau=="reg") $parametres3="region.idRegion";
				elseif ($this->niveau=="pays") $parametres3="pays.idPays";
				else $parametres3="null";

				$colonnesBDD=array();
				$colonnesBDD[]="rp.idSource";
				$colonnesBDD[]="YEAR(election.dateElection)";
				if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
				$colonnesBDD[]=$parametres3;

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
		
		public function getComboParticipation(){
		
			if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
			elseif ($this->typeElection=="legislative") $titreElection="législative";
			elseif ($this->typeElection=="regionale") $titreElection="régionale";
			else $titreElection=$typeElection;
		
			if ($this->niveau=="cen") $nomLieu="nomCentre,";
			elseif ($this->niveau=="dep") $nomLieu="nomDepartement,";
			elseif ($this->niveau=="reg") $nomLieu="nomRegion,";
			elseif ($this->niveau=="pays") $nomLieu="nomPays,";
			else $nomLieu="'Participation au niveau  national' as nomLieu,";
		
			if(!empty($_GET['param'])){
				$parametres=$_GET['param'];
			}
			else return ;
		
			$params=explode(",",$parametres);
			$v=0;
		
			$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
			FROM {$this->tablesParticipation[$this->typeElection]} rp
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		
		
			if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
				$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
				LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($this->niveau=="reg" OR $this->niveau=="pays")
				$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($this->niveau=="pays")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		
				if ($this->niveau=="cen") $parametres3="centre.idCentre";
				elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
				elseif ($this->niveau=="reg") $parametres3="region.idRegion";
				elseif ($this->niveau=="pays") $parametres3="pays.idPays";
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
				$titre_niveau.=": Taux de participation ";
		
				if ($this->niveau=="cen") {
				$titre_niveau.="par centre ";$sous_titre="Centre: ";
				}
				elseif ($this->niveau=="dep") {
						$titre_niveau.="par département ";$sous_titre="Département: ";
				}
				elseif($this->niveau=="reg") {
				$titre_niveau.="par région";$sous_titre="Région: ";
				}
				elseif($this->niveau=="pays") {
				$titre_niveau.="par pays ";$sous_titre="Pays: ";
		}
		else  $titre_niveau.="au niveau national ";
		
		if ($this->niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
				elseif ($this->niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
				elseif ($this->niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
				elseif ($this->niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
				else $sous_titre="";
				$titre=$titre_niveau;
		
		
				// ----------------------------------------	//
				//			COLLECTE DES DONNEES			//
				// ----------------------------------------	//
		
				$i=0;$j=0;
		
				if(!empty($_GET['unite'])){
						if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";
		
						// ----------------------------------------	//
						//			COLLECTE DES DONNEES			//
						// ----------------------------------------	//
		
							$abscisse=array();$ordonnee=array();$data_ordonnee=array();
		
							foreach ($resultats as $resultat){
		
							$source=$resultat->nomSource;
							$sous_titre.=" | Source:".$source;
		
							$barData[]=array("y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[0]}");
							$barData[]=array("y"=>(int)$resultat->votants,"color"=>"{$this->colors[1]}");
							$barData[]=array("y"=>(int)$resultat->nuls,"color"=>"{$this->colors[2]}");
							$barData[]=array("y"=>(int)$resultat->exprimes,"color"=>"{$this->colors[3]}");
		
							$pieData[]=array("name"=>"Votants","y"=>(int)$resultat->votants,"sliced"=>true,"selected"=>true,"color"=>"{$this->colors[0]}");
							$pieData[]=array("name"=>"Abstention","y"=>(int)$resultat->abstention,"color"=>"{$this->colors[1]}");
							$pieData2[]=array("name"=>"Nuls","y"=>(int)$resultat->nuls,"sliced"=>true,"selected"=>true,"color"=>"{$this->colors[2]}");
							$pieData2[]=array("name"=>"Suffrages exprimés","y"=>(int)$resultat->exprimes,"color"=>"{$this->colors[3]}");
		
		}
		
							$rendu=array();
		
							$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre);
							$rendu[]=array("type"=>"column","name"=>"Informations sur la participation","data"=>$barData);
							$rendu[]=array("type"=>"pie","name"=>"Abstention - Votants","data"=>$pieData,"size"=>100,"center"=>array(610,90));
							$rendu[]=array("type"=>"pie","name"=>"Nuls - Exprimés","data"=>$pieData2,"size"=>100,"center"=>array(340,90));
		
		echo json_encode($rendu);
		
		} // ...............  Fin de getComboParticipation() ...............
		

		public function getPoidsElectoralRegions(){
			
			if (!empty($_GET['annee']) AND !empty($_GET['tour'])) {$annee=$_GET['annee'];$tour=$_GET['tour'];} else return;
			
			$requete="SELECT nomRegion, SUM( nbInscrits ) as inscrits
			FROM {$this->tablesParticipation[$this->typeElection]} rp
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
			if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
			elseif ($this->typeElection=="legislative") $titreElection="législative";
			elseif ($this->typeElection=="regionale") $titreElection="régionale";
			else $titreElection=$typeElection;
			$titre_niveau.=" $titreElection ".$annee;
			$sous_titre="Poids électoral des régions"; 		
			$titre=$titre_niveau;
		
			// ----------------------------------------	//
			//			COLLECTE DES DONNEES			//
			// ----------------------------------------	//
		
			$i=0;

			$pieData=array();
			foreach ($resultats as $resultat){
				if($i)
					$pieData[]=array("name"=>$resultat->nomRegion,"y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[$i++]}");
				else
					$pieData[]=array("name"=>$resultat->nomRegion,"y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[$i++]}","sliced"=> true,"selected"=>true);
			}
					
			$rendu=array();
			$rendu[]=array( "titre"=>$titre ,"sous_titre"=> $sous_titre);
			$rendu[]=array("type"=>"pie","name"=>$titre,"data"=>$pieData,"size"=>190,"center"=>array("50%","45%"));
			
			echo json_encode($rendu);
					
		
		} // ...............  Fin de getPoidsElectoralRegions() ...............
		
		
		
		public function exportStatisticsToCSV(){
		
			if ($this->niveau=="cen") $nomLieu="nomCentre as nomLieu,";
			elseif ($this->niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
			elseif ($this->niveau=="reg") $nomLieu="nomRegion as nomLieu,";
			elseif ($this->niveau=="pays") $nomLieu="nomPays as nomLieu,";
			else $nomLieu="'Participation au niveau  national' as nomLieu,";
		
			if(!empty($_GET['param'])){
				$parametres=$_GET['param'];
				$params=explode(",",$parametres);
				$v=0;
				$parametres3=null;
		
				$requete="SELECT rp.idElection,YEAR(dateElection) as annee, $nomLieu nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
				FROM {$this->tablesParticipation[$this->typeElection]} rp
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
				if ($this->niveau=="dep" OR $this->niveau=="reg" OR $this->niveau=="pays")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
					if ($this->niveau=="reg" OR $this->niveau=="pays")
							$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
							if ($this->niveau=="pays")
									$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
									if ($this->niveau=="cen") $parametres3="centre.idCentre";
									elseif ($this->niveau=="dep") $parametres3="departement.idDepartement";
									elseif ($this->niveau=="reg") $parametres3="region.idRegion";
									elseif ($this->niveau=="pays") $parametres3="pays.idPays";
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