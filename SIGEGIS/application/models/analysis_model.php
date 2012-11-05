<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author Amadou SOW && Abdou Khadre GUEYE | DESS 2ITIC 2011-2012
 *
 */
class Analysis_model extends CI_Model{	
	private $titre;
	private $sous_titre;
	private $titreElection;
	private $tableCandidat;
	private $candidatOrListe=array("candidat"=>"idCandidature","listescoalitionspartis"=>"idListe");
	private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
	private $tables=array("presidentielle"=>"resultatspresidentielles2","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
	private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
	
	public function __construct(){
		$this->titre=""; $this->sous_titre=""; $this->titreElection="";
		if(!empty($_GET["typeElection"])) {
			$this->typeElection=$_GET["typeElection"];
			if ($this->typeElection=="presidentielle") {
				$this->tableCandidat="candidat";
				$this->titreElection="présidentielle";
			}
			elseif ($this->typeElection=="legislative") $this->titreElection="législative";
			elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
			else {
				$this->titreElection=$this->typeElection;
				$this->tableCandidat="listescoalitionspartis";
			}
		} else $this->typeElection=null;
	}
	
	public function isPresidentielle(){
		return ($this->typeElection=="presidentielle")?true:false;
	}
	
	public static function nomLieu($niveau,$default=""){
		if ($niveau=="cen") $nomLieu="nomCentre as nomLieu,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
		elseif ($niveau=="reg") $nomLieu="nomRegion as nomLieu,";
		elseif ($niveau=="pays") $nomLieu="nomPays as nomLieu,";
		else $nomLieu=$default ;
		return $nomLieu;
	}
	
	public static function attributLocalite($niveau,$default=""){
		$attributLocalite=null;
		if ($niveau=="cen") $attributLocalite="centre.idCentre";
		elseif ($niveau=="dep") $attributLocalite="departement.idDepartement";
		elseif ($niveau=="reg") $attributLocalite="region.idRegion";
		elseif ($niveau=="pays") $attributLocalite="pays.idPays";
		else $attributLocalite=$default;
		return $attributLocalite;
	}
	
	public static function attributNomLocalite($niveau,$default=""){
		$attributLocalite=null;
		if ($niveau=="cen") $attributLocalite="centre.nomCentre";
		elseif ($niveau=="dep") $attributLocalite="departement.nomDepartement";
		elseif ($niveau=="reg") $attributLocalite="region.nomRegion";
		elseif ($niveau=="pays") $attributLocalite="pays.nomPays";
		else $attributLocalite=$default;
		return $attributLocalite;
	}
		
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getBarAnalyserAnnee($typeElection,$niveau,$params){
			
		$barSeries=array();
		
		if(!empty($params) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
				
			$v=0;

			$colonnesBDD[]="rp.idSource";
			if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
			if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
			
			$couleur=0;
			
			foreach ($listeCandidats as $leCandidat){
				$v=0;
				
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				
				if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";
				else $requete.="nomListe";

				$requete.=" as nomCandidat,rp.idCentre ,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";
					
				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays" OR $niveau=="globaux")
					$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
					
				for($i=0;$i<sizeof($params);$i++) {
					if($v) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";$v++;}
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

		/* ------------------------------------	*/
		/*			TITRE DU DIAGRAMME			*/
		/* ------------------------------------	*/
		
		asort($listeAnnees); // Ordonner les annees selectionnees
		
		$categories=array_values($listeAnnees);	// Les mettre en abscisse
		
		$this->titre="Election";
		if(sizeof($listeAnnees)>1) $this->titre.="s"; $this->titre.=" $this->titreElection";
		if(sizeof($listeAnnees)>1) $this->titre.="s"; 
		$this->titre.=" ".htmlentities(implode(",", $categories));
		$titre_niveau="";
		if ($niveau=="cen") {
			$titre_niveau.="par centre ";$this->sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			$titre_niveau.="départementaux ";$this->sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			$titre_niveau.="régionaux ";$this->sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			$titre_niveau.="par pays ";$this->sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";

		if ($niveau) $this->sous_titre.=  $resultats[0]->nomLieu;

		/* ------------------------------------	*/
		/*		    COLLECTE DES DONNEES		*/
		/* ------------------------------------	*/
		
		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";

		/* ------------------------------------	*/
		/*				   RENDU				*/
		/* ------------------------------------	*/
		
		$rendu=array();
		$rendu[]=array("titre"=>$this->titre,"sous_titre"=>$this->sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		
		echo json_encode($rendu);
		
	} // ............... getBarAnalyserAnnee ...............

	/**
	 * Cette fonction affiche le code xml du Grid
	 * @return string
	 */
	public function getGridAnalyserAnnee($typeElection,$niveau,$params){

		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];

		if(!$sidx) $sidx =1;

		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";

		if(!empty($params) AND !empty($_GET['listeAnnees']) AND !empty($_GET['listeCandidats'])){
			
			$listeAnnees=explode(",",$_GET['listeAnnees']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
		
			$colonnesBDD[]="rp.idSource";
			if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
			if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);		

			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
				if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat, rp.idCentre,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
				LEFT JOIN source ON rp.idSource = source.idSource
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre";

				if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
					$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
					LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
				if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
					$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				if ($niveau=="pays" OR $niveau=="globaux")
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

				$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY YEAR(dateElection),rp.idCandidature ORDER BY dateElection ASC";

				$tableauResultats[]=$this->db->query($requete)->result();
			}
		}

		$totalRows=sizeof($listeAnnees)*sizeof($listeCandidats);

		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;

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
				$s .= "<cell>". $row->nomLieu ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";

		echo $s;
	} // ............... getGridAnalyserAnnee() ...............
	
	/**
	 * Cette fonction retourne le code JavaScript du Column chart
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getBarAnalyserLocalite($typeElection,$niveau,$params,$listeLocalites,$listeCandidats){

		$barSeries=array();
		$categories=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";		

		$couleur=0;
				
		$categories=$listeLocalites;
		
		$v=0;

		$colonnesBDD[]="rp.idSource";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		$colonnesBDD[]="YEAR(election.dateElection)";
		$colonnesBDD[]="election.typeElection";
					
		
		foreach ($listeCandidats as $leCandidat){

			$v=0;
			$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
			if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			$requete.=" as nomCandidat, rp.idCentre ,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
			FROM {$this->tables[$typeElection]} rp
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";

			if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
				LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

			for($i=0;$i<sizeof($params);$i++) {
				if ($v) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";			
				else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";$v++;}
			}

			$theYear="";
			foreach ($listeLocalites as $laLocalite){
				if ($theYear=="") $theYear.=" AND ( ".self::attributNomLocalite($niveau)."='".$laLocalite."'";
				else $theYear.= " OR ".self::attributNomLocalite($niveau)."='".$laLocalite."'";
			}
			$requete.=$theYear.")";
			$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, ".self::attributLocalite($niveau)." ORDER BY rp.idCandidature";

			$resultats=$this->db->query($requete)->result();

			$data=array();
			
			foreach ($resultats as $resultat){
				$data[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$couleur]}");
			}

			$barSeries[]=array("name"=>$resultat->nomCandidat, "data"=>$data);
			$couleur++;
		}
	

		/* ------------------------------------	*/
		/*			TITRE DU DIAGRAMME			*/
		/* ------------------------------------	*/
		$titre_niveau="Election ".$this->titreElection." ".htmlentities($params[2]);
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

		/* ------------------------------------	*/
		/*				   RENDU				*/
		/* ------------------------------------	*/
		$rendu=array();
		$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		echo json_encode($rendu);
		
	}// ...............  Fin de getBarAnalyserLocalite() ...............
	
	/**
	 * Cette fonction affiche le code xml du Grid
	 * @return string
	 */
	public function getGridAnalyserLocalite($typeElection,$niveau,$params,$listeLocalites,$listeCandidats){

		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];

		if(!$sidx) $sidx =1;

		$tableauResultats=array();
		$series="";
		$titre="";
		$sous_titre="";
		$unite="";
		$abscisse="";
				
		$v=0;

		$colonnesBDD[]="rp.idSource";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		$colonnesBDD[]="YEAR(election.dateElection)";
		$colonnesBDD[]="election.typeElection";

		foreach ($listeCandidats as $leCandidat){

			$v=0;
			$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
			
			if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			
			$requete.=" as nomCandidat, rp.idCentre,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
			FROM {$this->tables[$typeElection]} rp
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";

			if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
				LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays" OR $niveau=="globaux")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

			for($i=0;$i<sizeof($params);$i++) {
				if($v) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";					
				else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";$v++;}
			}

			$theYear="";
			foreach ($listeLocalites as $laLocalite){
				if ($theYear=="") $theYear.=" AND (".self::attributNomLocalite($niveau)."='".$laLocalite."'";
				else $theYear.= " OR ".self::attributNomLocalite($niveau)."='".$laLocalite."'";
			}
			
			$requete.=$theYear.")";
			$requete.=" AND rp.idCandidature=".$leCandidat." GROUP BY rp.idCandidature,annee, ".self::attributNomLocalite($niveau)." ORDER BY rp.idCandidature";

			$tableauResultats[]=$this->db->query($requete)->result();
		}
	
		$totalRows=sizeof($listeLocalites)*sizeof($listeCandidats);

		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;

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
				$s .= "<cell>". $row->nomLieu ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";
		echo $s;
	} // ............... getGridAnalyserLocalite() ...............					
}