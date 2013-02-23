<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author Amadou SOW && Abdou Khadre GUEYE | DESS 2ITIC 2011-2012
 *
 */
class Analyser_model extends CI_Model{	
	private $candidatOrListe=array("candidat"=>"idCandidat","listescoalitionspartis"=>"idListe");
	private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
	private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales"); // Resultats enregistres 
	private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales"); // Statistiques
	private $titre;
	private $sous_titre;
	private $titreElection;

	public function __construct(){
		$this->titre=""; $this->sous_titre=""; $this->titreElection="";
		if(!empty($_GET["typeElection"]))
		if ($_GET["typeElection"]=="presidentielle") $this->titreElection="présidentielle";
		elseif ($_GET["typeElection"]=="legislative") $this->titreElection="législative";
		elseif ($_GET["typeElection"]=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$this->typeElection;
	}
	
	/**
	 * Fournit le nom du lieu de vote
	 * @param string $niveau
	 * @param string $default
	 */
	public static function nomLieu($niveau,$default=""){
		if ($niveau=="cen") $nomLieu="nomCentre as nomLieu,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement as nomLieu,";
		elseif ($niveau=="reg") $nomLieu="nomRegion as nomLieu,";
		elseif ($niveau=="pays") $nomLieu="nomPays as nomLieu,";
		else $nomLieu=$default ;
		return $nomLieu;
	}
	
	/**
	 * Fournit l'attribut dans la BDD correspondant à l'ID de la localité
	 * @param string $niveau
	 * @param string $default L'attribut par défaut
	 * @return string L'attribut de la BDD correspondant à l'ID de la localité
	 */
	public static function attributLocalite($niveau,$default=""){
		$attributLocalite=null;
		if ($niveau=="cen") $attributLocalite="centre.idCentre";
		elseif ($niveau=="dep") $attributLocalite="departement.idDepartement";
		elseif ($niveau=="reg") $attributLocalite="region.idRegion";
		elseif ($niveau=="pays") $attributLocalite="pays.idPays";
		else $attributLocalite=$default;
		return $attributLocalite;
	}
	
	/**
	 * Fournit l'attribut dans la BDD correspondant au nom de la localité
	 * @param string $niveau
	 * @param string $default L'attribut par défaut
	 * @return string L'attribut de la BDD correspondant au nom de la localité
	 */
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
	 * Cette fonction retourne la concatenation d'une portion de requete avec la portion des jointures necessaires a la recuperation des données<br />
	 * Une jointure se fait sur les colonnes idCentre et idDepartement des tables resultats, centre et departement<br/>	 
	 * @param string $requete la portion de la requete a concatener avec les jointures LEFT JOIN
	 * @param string $niveau
	 * @param string $granularite (centre|departement)
	 */
	public function concatLeftJoinTo($requete,$niveau,$tableCandidat=null){		
				
		if ($tableCandidat) 
			$requete.=" LEFT JOIN {$tableCandidat} ON rp.idCandidat = {$tableCandidat}.{$this->candidatOrListe[$tableCandidat]}"; 
		$requete .=" LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN centre ON rp.idCentre = centre.idCentre
		LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite";
	
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays"OR $niveau=="globaux")
			$requete.="	LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement OR rp.idDepartement = departement.idDepartement ";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		return $requete;
	}
	
	// DEUXIEME VERSION DE CONCAT / PLUS RAPIDE
	// ATTENTION: CHOISIR CETTE METHODE SEULEMENT DANS LE CAS OU TOUTES LES DONNEES DE SIGEGIS SONT
	// SOIT EXCLUSIVEMENT DE GRANULARITE CENTRE
	// SOIT EXCLUSIVEMENT DE GRANULARITE DEPARTEMENT
	// COMPLETER LES APPELS CONCAT AVEC LE PARAMETRE $granularite AU NIVEAU DU PRESENT CODE DANS LE CAS ECHEANT 
	/*
	 public function concatLeftJoinTo($requete,$niveau,$tableCandidat=null,$granularite){
		 if ($tableCandidat) 
			$requete.=" LEFT JOIN {$tableCandidat} ON rp.idCandidat = {$tableCandidat}.{$this->candidatOrListe[$tableCandidat]}";
		$requete.=" LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN source ON rp.idSource = source.idSource";
	
		if ($granularite=="centre"){
			$requete.=" LEFT JOIN centre ON rp.idCentre = centre.idCentre
			LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite";
	
			if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays"OR $niveau=="globaux")
				$requete.="	LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		}
		else
		{
			$requete.="	LEFT JOIN departement ON rp.idDepartement = departement.idDepartement";
		}
	
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		return $requete;
	}*/
		
	/**
	 * Diagramme en bâtons
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @param array $listeAnnees
	 * @param array $listeCandidats
	 * @return string Objet JSON
	 */
	public function getBarAnalyserSuivantAnnee($typeElection,$niveau,$params,$granularite,$listeAnnees,$listeCandidats,$tableCandidat){
			
		$barSeries=array();$nomLieu="";
		
		if(!empty($params) AND !empty($listeAnnees) AND !empty($listeCandidats))
		{							
			$v=0;

			$colonnesBDD[]="rp.idSource";
			if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";
			if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
			
			$couleur=0;
			
			foreach ($listeCandidats as $leCandidat){
				$v=0;
				
				$requete="SELECT rp.idCandidat, YEAR(dateElection) as annee, ";
				
				if ($typeElection="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
				else $requete.="nomListe";

				$requete.=" as nomCandidat,rp.idCentre ,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp";
				
				$requete=$this->concatLeftJoinTo($requete, $niveau, $tableCandidat);
					
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
					
				$requete.=" AND rp.idCandidat=".$leCandidat."
				GROUP BY YEAR(dateElection),rp.idCandidat ORDER BY dateElection ASC";
					
				$resultats=$this->db->query($requete)->result();									
					
				$data=array();
				$nomLieu=$resultats[0]->nomLieu;
				
				foreach ($resultats as $resultat){
					$data[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$couleur]}");					
				}

				$barSeries[]=array("name"=>$resultat->nomCandidat, "data"=>$data,"color"=>"{$this->colors[$couleur]}");
				$couleur++;
			}			
		}

		// TITRE DU DIAGRAMME
		
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

		if ($niveau) $this->sous_titre.=  $nomLieu;

		// RENDU
		
		$rendu=array();
		$rendu[]=array("titre"=>$this->titre,"sous_titre"=>$this->sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		
		echo json_encode($rendu);
		
	} // ............... getBarAnalyserSuivantAnnee ...............

	/**
	 * Retourne les données pour le grid de l'analyse suivant une année
	 * @param $typeElection string le type de l'élection à considérer
	 * @param $niveau string le niveau d'agrégation des données
	 * @param $params array tableau contenant successivement l'ID de la source, l'année de l'élection et si nécessaire le tour
	 * @param array $listeAnnees
	 * @param array $listeCandidats
	 * @return string Code XML	 
	 */
	public function getGridAnalyserSuivantAnnee($typeElection,$niveau,$params,$granularite,$listeAnnees,$listeCandidats,$tableCandidat){

		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];

		if(!$sidx) $sidx =1;

		$tableauResultats=array();
		$series="";		$titre="";		$sous_titre="";		$unite="";		$abscisse="";

		if(!empty($params) AND !empty($listeAnnees) AND !empty($listeCandidats)){
			
			$listeAnnees=explode(",",$listeAnnees);
			$listeCandidats=explode(",",$listeCandidats);
		
			$colonnesBDD[]="rp.idSource";
			if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";
			if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);		

			foreach ($listeCandidats as $leCandidat){
				$v=0;
				$requete="SELECT rp.idCandidat, YEAR(dateElection) as annee, ";
				if ($typeElection="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
				else $requete.="nomListe";
				$requete.=" as nomCandidat, rp.idCentre,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
				FROM {$this->tables[$typeElection]} rp";
				
				$requete=$this->concatLeftJoinTo($requete, $niveau, $tableCandidat);

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

				$requete.=" AND rp.idCandidat=".$leCandidat." GROUP BY YEAR(dateElection),rp.idCandidat ORDER BY dateElection ASC";

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
				$s .= "<row id='". $row->idCandidat ."'>";
				$s .= "<cell>". $row->nomCandidat ."</cell>";
				$s .= "<cell>". $row->nomLieu ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";

		echo $s;
	} // ............... getGridAnalyserSuivantAnnee() ...............
	
	/**
	 * Diagramme en bâtons
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @return string Objet JSON
	 */
	public function getBarAnalyserSuivantLocalite($typeElection,$niveau,$params,$granularite,$listeLocalites,$listeCandidats,$tableCandidat){

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
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";					

		foreach ($listeCandidats as $leCandidat){

			$v=0;
			$requete="SELECT rp.idCandidat, YEAR(dateElection) as annee, ";
			if ($typeElection="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			$requete.=" as nomCandidat, rp.idCentre ,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
			FROM {$this->tables[$typeElection]} rp";
			
			$requete=$this->concatLeftJoinTo($requete, $niveau, $tableCandidat);

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
			$requete.=" AND rp.idCandidat=".$leCandidat." GROUP BY rp.idCandidat,annee, ".self::attributLocalite($niveau)." ORDER BY rp.idCandidat";

			$resultats=$this->db->query($requete)->result();

			$data=array();
			if ($resultats){
			foreach ($resultats as $resultat){
				$data[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$couleur]}");
			}

			$barSeries[]=array("name"=>$resultat->nomCandidat, "data"=>$data);
			$couleur++;
			}
		}
	
		// TITRE DU DIAGRAMME
		
		$titre_niveau="Election ".$this->titreElection." ".htmlentities($params[1]);
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

		// RENDU
		
		$rendu=array();
		$rendu[]=array("titre"=>$titre,"sous_titre"=>$sous_titre,"categories"=>$categories);
		$rendu[]=$barSeries;		// series[1]
		echo json_encode($rendu);
		
	}// ...............  Fin de getBarAnalyserSuivantLocalite() ...............
	
	/**
	 * Retourne les données pour le grid de l'analyse suivant une localité
	 * @param $typeElection string le type de l'élection à considérer
	 * @param $niveau string le niveau d'agrégation des données
	 * @param $params array tableau contenant successivement l'ID de la source, l'année de l'élection et si nécessaire le tour
	 * @return string Code XML	 
	 */
	public function getGridAnalyserSuivantLocalite($typeElection,$niveau,$params,$granularite,$listeLocalites,$listeCandidats,$tableCandidat){

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
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";		

		foreach ($listeCandidats as $leCandidat){

			$v=0;
			$requete="SELECT rp.idCandidat, YEAR(dateElection) as annee, ";
			
			if ($typeElection="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			
			$requete.=" as nomCandidat, rp.idCentre,".self::nomLieu($niveau)." nomSource,  SUM(nbVoix) as nbVoix
			FROM {$this->tables[$typeElection]} rp";

			$requete=$this->concatLeftJoinTo($requete, $niveau, $tableCandidat);

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
			$requete.=" AND rp.idCandidat=".$leCandidat." GROUP BY rp.idCandidat,annee, ".self::attributNomLocalite($niveau)." ORDER BY rp.idCandidat";

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
				$s .= "<row id='". $row->idCandidat ."'>";
				$s .= "<cell>". $row->nomCandidat ."</cell>";
				$s .= "<cell>". $row->nomLieu ."</cell>";
				$s .= "<cell>". $row->annee ."</cell>";
				$s .= "<cell>". $row->nbVoix ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";
		echo $s;
	} // ............... getGridAnalyserSuivantLocalite() ...............

	/**
	 * Cette fonction affiche le code xml du Grid
	 * @return string
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridParticipation($typeElection,$niveau,$params,$granularite){
	
		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];
		$v=0;
	
		if(!$sidx) $sidx =1;
	
		$default="'Participation au niveau national' as nomLieu,";
	
		$requete="SELECT rp.idElection, 
		typeElection, 
		YEAR(dateElection) as annee, 
		".self::nomLieu($niveau,$default)." nomSource,
		sum(nbInscrits) as inscrits,
		sum(nbVotants) as votants,
		sum(nbBulletinsNuls) as nuls,
		sum(nbExprimes) as exprimes,
		(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp";
	
		$requete=$this->concatLeftJoinTo($requete, $niveau);
	
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
		$colonnesBDD[]="election.typeElection";

		for($i=0;$i<sizeof($params);$i++) {
			if($v){
					if ($colonnesBDD[$i]) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
		}
		
		$totalRows=1;
		
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit); else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;

		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";

		$resultats=$this->db->query($requete)->result();

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
	} // ............... getGridParticipation() ...............
	
	/**
	* Composition de diagrammes fournissant de infos sur la participation
	* @param string $typeElection
	* @param string $niveau
	* @param array $params
	* @return string Objet JSON
	*/
	public function getComboParticipation($typeElection,$niveau,$params,$granularite){

		$default="'Participation au niveau  national' as nomLieu,";	$v=0;

		$requete="SELECT rp.idElection, 
		typeElection, 
		YEAR(dateElection) as annee, 
		".self::nomLieu($niveau,$default)." nomSource,
		sum(nbInscrits) as inscrits,
		sum(nbVotants) as votants,
		sum(nbBulletinsNuls) as nuls,
		sum(nbExprimes) as exprimes,
		(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp";
		
		// CONCATENATION 
		$requete=$this->concatLeftJoinTo($requete, $niveau);
		
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
		$colonnesBDD[]="election.typeElection";
	
		for($i=0;$i<sizeof($params);$i++) {
			if($v){
			if ($colonnesBDD[$i]) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
				else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;
			}
		}
		
		$resultats=$this->db->query($requete)->result();
		
		// TITRE DU DIAGRAMME
		
		$this->titre="Election $this->titreElection ".$resultats[0]->annee;
		$this->titre.=": Taux de participation ";
		
		if ($niveau=="cen") {
			$this->titre.="par centre ";$this->sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			$this->titre.="par département ";$this->sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			$this->titre.="par région";$this->sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			$this->titre.="par pays ";$this->sous_titre="Pays: ";
		}
		else  $this->titre.="au niveau national ";
		
		if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
		} else  $unite="En valeurs absolues";
		
		// COLLECTE DES DONNEES
	
		foreach ($resultats as $resultat){
			$source=$resultat->nomSource;
			$this->sous_titre.=$resultat->nomLieu." | Source:".$source;
	
			$barData[]=array("y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[0]}");
			$barData[]=array("y"=>(int)$resultat->votants,"color"=>"{$this->colors[1]}");
			$barData[]=array("y"=>(int)$resultat->nuls,"color"=>"{$this->colors[2]}");
			$barData[]=array("y"=>(int)$resultat->exprimes,"color"=>"{$this->colors[3]}");
	
			$pieData[]=array("name"=>"Votants","y"=>(int)$resultat->votants,"sliced"=>true,"selected"=>true,"color"=>"{$this->colors[0]}");
			$pieData[]=array("name"=>"Abstention","y"=>(int)$resultat->abstention,"color"=>"{$this->colors[1]}");
			$pieData2[]=array("name"=>"Nuls","y"=>(int)$resultat->nuls,"sliced"=>true,"selected"=>true,"color"=>"{$this->colors[2]}");
			$pieData2[]=array("name"=>"Suffrages exprimés","y"=>(int)$resultat->exprimes,"color"=>"{$this->colors[3]}");
		}
		
		$rendu[]=array("titre"=>$this->titre,"sous_titre"=>$this->sous_titre);
		$rendu[]=array("type"=>"column","name"=>"Informations sur la participation","data"=>$barData);
		$rendu[]=array("type"=>"pie","name"=>"Abstention - Votants","data"=>$pieData,"size"=>100,"center"=>array(510,90));
		$rendu[]=array("type"=>"pie","name"=>"Nuls - Exprimés","data"=>$pieData2,"size"=>100,"center"=>array(240,90));
		
		echo json_encode($rendu);

	} // ...............  Fin de getComboParticipation() ...............

	/**
	* Répartion géographique des électeurs (diagramme circulaire)
	* @param string $typeElection
	* @param string $niveau
	* @param string $annee
	* @param string $tour
	* @param array $params
	* @return string Objet JSON
	*/
	public function getPoidsElectoralRegions($typeElection,$niveau,$granularite,$annee,$tour){

	$requete="SELECT nomRegion, YEAR(dateElection) as annee, SUM( nbInscrits ) as inscrits
	FROM {$this->tablesParticipation[$typeElection]} rp
	LEFT JOIN election ON rp.idElection = election.idElection";
	if ($granularite=="centre") 
		$requete.=" LEFT JOIN centre ON rp.idCentre = centre.idCentre
		LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite 
		LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
	else 
		$requete.=" LEFT JOIN departement ON rp.idDepartement = departement.idDepartement";
	$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion
	WHERE YEAR(dateElection)=$annee AND nomRegion<>'ETRANGER' AND election.tour='$tour'
	GROUP BY region.idRegion ORDER BY nomRegion";

	$resultats=$this->db->query($requete)->result();
	
	// TITRE DU DIAGRAMME
	
	$defaultTitle="Poids électoral des régions";
	list($this->titre,$this->sous_titre)=self::titre($resultats, $this->titreElection, $niveau, $defaultTitle);
			
	// COLLECTE DES DONNEES	
							
	$i=0; $pieData=array();
	
	foreach ($resultats as $resultat){
	if($i)
	$pieData[]=array("name"=>$resultat->nomRegion,"y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[$i++]}");
	else
	$pieData[]=array("name"=>$resultat->nomRegion,"y"=>(int)$resultat->inscrits,"color"=>"{$this->colors[$i++]}","sliced"=> true,"selected"=>true);
	}
	
	$rendu=array();
	$rendu[]=array( "titre"=>$this->titre ,"sous_titre"=> $this->sous_titre);
	$rendu[]=array("type"=>"pie","name"=>$this->titre,"data"=>$pieData,"size"=>190,"center"=>array("50%","45%"));
	
	echo json_encode($rendu);
	
	} // ............... getPoidsElectoralRegions() ...............
	
	/**
	 *
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 */
	public function exportStatisticsToCSV($typeElection,$niveau,$params,$granularite){
	
		$default="'Participation au niveau  national' as nomLieu,";	$v=0;
	
		$requete="SELECT rp.idElection,YEAR(dateElection) as annee, ".self::nomLieu($niveau,$default)." nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp";
	
		$requete=$this->concatLeftJoinTo($requete, $niveau, $tableCandidat);
	
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($typeElection="presidentielle") $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);

		for($i=0;$i<sizeof($params);$i++) {
		if($v){
		if ($colonnesBDD[$i]) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
		}
		else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
	}
	
	$resultats=$this->db->query($requete)->result();
	
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
	} // ............... exportStatisticsToCSV() ...............
	
	public static function titre($resultats,$titreElection,$niveau,$defaultTitle="",$defaultSubTitle=""){
	
		if (!$resultats) return array("Données indisponibles","Réessayez plus tard | ");
	
		$titre_niveau="Election "; $sous_titre="";
	
		$titre_niveau.=" $titreElection ".$resultats[0]->annee.": résultats ";
	
		if ($niveau=="cen") {
			$titre_niveau.="par centre "; $sous_titre="Centre: ";
		}
		elseif ($niveau=="dep") {
			$titre_niveau.="départementaux "; $sous_titre="Département: ";
		}
		elseif($niveau=="reg") {
			$titre_niveau.="régionaux "; $sous_titre="Région: ";
		}
		elseif($niveau=="pays") {
			$titre_niveau.="par pays "; $sous_titre="Pays: ";
		}
		else  $titre_niveau.="globaux ";
	
		if ($niveau) $sous_titre=  $resultats[0]->nomLieu;
	
		if($defaultTitle!="") $titre_niveau=$defaultTitle;
	
		if($defaultSubTitle!="") $titre_niveau=$defaultSubTitle;
	
		return array($titre_niveau,$sous_titre);
	}
	
}