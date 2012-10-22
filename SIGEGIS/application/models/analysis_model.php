<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author Amadou SOW && Abdou Khadre GUEYE | DESS 2ITIC 2011-2012
 *
 */
class Analysis_model extends CI_Model{
	private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
	private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
	private $candidatOrListe=array("candidature"=>"idCandidature","listescoalitionspartis"=>"idListe");
	private $tableCandidat;
	private $typeElection;
	private $niveau;
	
public function __construct(){
	if(!empty($_GET["typeElection"])) {
		$this->typeElection=$_GET["typeElection"];	
		if ($this->typeElection=="presidentielle") $this->tableCandidat="candidature";else $this->tableCandidat="listescoalitionspartis";
		if ($this->typeElection=="presidentielle") $this->titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $this->titreElection="législative";
		elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$this->typeElection;		
	}
		
	if(!empty($_GET['niveau'])) $this->niveau=$_GET['niveau'];
}
	
	
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getBarAnalyserAnnee(){
			
		$barSeries=array();
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";


		if(!empty($_GET["typeElection"])) $this->typeElection=$_GET["typeElection"];	else return;
		
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection."";		

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
				
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]=$parametres3;
			
			$couleur=0;
			
			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				if ($this->typeElection=="presidentielle") $requete.="CONCAT(prenom, '', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat,rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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
					
				$requete.=" AND rp.idCandidature=".$leCandidat."
				GROUP BY YEAR(dateElection),rp.idCandidature ORDER BY dateElection ASC";
					
				$resultats=$this->db->query($requete)->result();									
					
				$data=array();
				
				foreach ($resultats as $resultat){
					$data[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$couleur]}");					
				}

				$barSeries[]=array("name"=>$resultat->nomCandidat, "data"=>$data,"color"=>"{$this->colors[$couleur]}");
				$couleur++;
			}			
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		
		asort($listeAnnees);
		
		$categories=array_values($listeAnnees);
		
		//var_dump($categories);
		
		$titre="Election";if(sizeof($listeAnnees)>1) $titre.="s"; $titre.=" $titreElection";if(sizeof($listeAnnees)>1) $titre.="s"; $titre.=" ".htmlentities(implode(",", $categories));
		$titre_niveau="";
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
		//$titre=$titre_niveau;


		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
			
		
		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
		
		$rendu=array();
		$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		
		echo json_encode($rendu);
		
	} // ...............  Fin de getBarAnalyserAnnee ...............


	/**
	 * Cette fonction retourne le code JavaScript du Pie chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */

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

		if(!empty($_GET["typeElection"])) $this->typeElection=$_GET["typeElection"];
		else return;
		
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection;		

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

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]=$parametres3;		

			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, CONCAT(prenom, ' ', nom) as nomCandidat, rp.idCentre,".substr($nomLieu,0,-1)." as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
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

				$requete.=" AND rp.idCandidature=".$leCandidat." 
				GROUP BY YEAR(dateElection),rp.idCandidature ORDER BY dateElection ASC";

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
	
		if(!empty($_GET["typeElection"])) $this->typeElection=$_GET["typeElection"];
		else return;
	
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection;
	
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
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, CONCAT(prenom, ' ', nom) as nomCandidat, rp.idCentre,".substr($nomLieu,0,-1)." as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
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
	
			$requete.=" AND rp.idCandidature=".$leCandidat." 
			GROUP BY YEAR(dateElection),rp.idCandidature ORDER BY dateElection ASC";
	
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
	public function getBarAnalyserLocalite(){

		$barSeries=array();
		$categories=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";		

		$couleur=0;
	
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection;		

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
			
			$categories=$listeLocalites;
			
			$v=0;

			if ($niveau=="cen") $parametres3="centre.nomCentre";
			elseif ($niveau=="dep") $parametres3="departement.nomDepartement";
			elseif ($niveau=="reg") $parametres3="region.nomRegion";
			elseif ($niveau=="pays") $parametres3="pays.nomPays";
			else $parametres3="null";

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]="YEAR(election.dateElection)";
			$colonnesBDD[]="election.typeElection";
						
			
			foreach ($listeCandidats as $leCandidat){

				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				if ($this->typeElection=="presidentielle") $requete.="CONCAT(prenom, '', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat, rp.idCentre ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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

				$data=array();
				
				foreach ($resultats as $resultat){
					$data[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$couleur]}");
				}

				$barSeries[]=array("name"=>$resultat->nomCandidat, "data"=>$data);
				$couleur++;
			}
		}

		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Election $titreElection ".htmlentities($params[2]);
		$sous_titre="Niveau d'agrégation des données: ";
		if ($niveau=="cen")
		{
			$sous_titre.="par centre";
		}
		elseif ($niveau=="dep")
		{
			$sous_titre.="par département";
		}
		elseif($niveau=="reg")
		{
			$sous_titre.="par région";
		}
		elseif($niveau=="pays")
		{
			$sous_titre.="par pays";
		}
		else  $titre_niveau.="Global";

		$titre=$titre_niveau;


		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
		$rendu=array();
		$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		echo json_encode($rendu);
		
	}// ...............  Fin de getBarAnalyserLocalite() ...............


	/**
	 * Cette fonction retourne le code JavaScript du Pie chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	
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


		if(!empty($_GET["typeElection"])) $this->typeElection=$_GET["typeElection"];
		else return;
		
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection;		

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

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]="YEAR(election.dateElection)";
			$colonnesBDD[]="election.typeElection";

			foreach ($listeCandidats as $leCandidat){

				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				if ($this->typeElection=="presidentielle") $requete.="CONCAT(prenom, '', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat, rp.idCentre,$parametres3 as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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
	} // ...............  Fin de getGridAnalyserLocalite() ...............

	
	public function exportToCSVLocalite(){
		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";
	
	
		if(!empty($_GET["typeElection"])) $this->typeElection=$_GET["typeElection"];
		else return;
	
		if ($this->typeElection=="presidentielle") $titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $titreElection="législative";
		elseif ($this->typeElection=="regionale") $titreElection="régionale";
		else $titreElection=$this->typeElection;
	
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
	
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if ($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]="YEAR(election.dateElection)";
			$colonnesBDD[]="election.typeElection";
	
			foreach ($listeCandidats as $leCandidat){
	
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				if ($this->typeElection=="presidentielle") $requete.="CONCAT(prenom, '', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat, rp.idCentre,$parametres3 as lieuDeVote ,$nomLieu nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$this->typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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
					
}