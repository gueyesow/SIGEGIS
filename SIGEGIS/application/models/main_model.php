<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description:  Fournit les données sur les élections
 * @author Amadou SOW && Abdou Khadre GUEYE DESS | 2ITIC 2011-2012 
 */
class Main_model extends CI_Model{
	private $titre;
	private $sous_titre;
	private $titreElection;
	private $tableCandidat;
	private $ypeElection;
	private $candidatOrListe=array("candidat"=>"idCandidature","listescoalitionspartis"=>"idListe");
	private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
	private $tables=array("presidentielle"=>"resultatspresidentielles2","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
	private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
	
	public function __construct(){
		$this->titre=""; $this->sous_titre=""; $this->titreElection="";
		if(!empty($_GET["typeElection"])) {
			$this->typeElection=$_GET["typeElection"];
			if ($this->typeElection=="presidentielle") $this->tableCandidat="candidat";else $this->tableCandidat="listescoalitionspartis";
			if ($this->typeElection=="presidentielle") {
				$this->titreElection="présidentielle";$this->tableCandidat="candidat";
			}
			elseif ($this->typeElection=="legislative") $this->titreElection="législative";
			elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
			else $this->titreElection=$this->typeElection;
		} else $this->typeElection=null;
	}
	/**
	 * @return array Tableau contenant le titre et le sous-titre du diagramme
	 * @param array $resultats
	 * @param string $titreElection
	 * @param string $niveau
	 * @param string $defaultTitle Le titre par défaut du diagramme
	 * @param string $defaultSubTitle Le sous-titre par défaut du diagramme
	 */
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
	
	public function isPresidentielle(){
		return ($this->typeElection=="presidentielle")?true:false;
	} 
	
	/**
	 * Fournit l'attibut de la BDD correspondant au nom de la localité
	 * @return string
	 * @param string $niveau
	 * @param string $default L'attribut par défaut (chaine vide)
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
	 * @param string $niveau
	 * @param string $default
	 * @return string L'attribut de la BDD correspondant a un type de localité
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
	 * 
	 * @param string $niveau
	 * @param string $default L'attribut par défaut
	 * @return string L'attribut de la BDD correspondant a un type de localité sans le prefixe de la table
	 */
	public static function attributLocalite2($niveau,$default=""){
		$attributLocalite=null;
		if ($niveau=="cen") $attributLocalite="idCentre";
		elseif ($niveau=="dep") $attributLocalite="idDepartement";
		elseif ($niveau=="reg") $attributLocalite="idRegion";
		elseif ($niveau=="pays") $attributLocalite="idPays";
		else $attributLocalite=$default;
		return $attributLocalite;
	}
	
	/**
	 * Diagramme en bâtons
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @return string Objet JSON
	 */
	public function getBarVisualiser($typeElection,$niveau,$params){
		$v=0;
		$default="'Résultats globaux' as nomLieu, ";
		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
	
		if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";	else $requete.="nomListe";
	
		$requete.=" as nomCandidat, ".self::nomLieu($niveau,$default)." nomSource,partis, SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays"OR $niveau=="globaux")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays"OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
			if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
	
			for($i=0;$i<sizeof($params);$i++) {
			if($v++){
			if ($colonnesBDD[$i]) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
	}
	
	$requete.=" GROUP BY rp.idCandidature";
	
	$resultats=$this->db->query($requete)->result();
	
			/* ----------------------------------------	*/
			/*			  TITRE DU DIAGRAMME			*/
			/* ----------------------------------------	*/
	
			list($this->titre,$this->sous_titre)=self::titre($resultats, $this->titreElection, $niveau);
	
			/* ----------------------------------------	*/
			/*			COLLECTE DES DONNEES			*/
			/* ----------------------------------------	*/
	
			$i=0;$j=0;
			$abscisse=array();$ordonnee=array();
	
			foreach ($resultats as $resultat){//ici
			$candidat=$resultat->nomCandidat;
			$a=preg_replace("#(.*)$params[1]:([a-zA-Z0-9 ]*)(.*)#", "$2", $resultat->partis);
			if ($this->isPresidentielle()) $candidat.="<br /><b>$a</b>";
			$abscisse[]=$candidat;
			$ordonnee[]=array("y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$i++]}",
					"url"=>"http://www.sigegis.ugb-edu.com/main_controller/getCandidat?id={$resultat->idCandidature}&typeElection={$typeElection}");
			}
	
			if(!empty($_GET['unite'])){
			if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";
			} else  $unite="En valeurs absolues";
	
			/* ----------------------------------------	*/
			//					RENDU					//
			/* ----------------------------------------	*/
	
			$rendu=array();
			$rendu["titre"]=$this->titre;
			$rendu["sous_titre"]=$this->sous_titre;
			$rendu["abscisse"]=$abscisse;
			$rendu["ordonnee"]=$ordonnee;
			$rendu["unite"]=$unite;
	
			echo json_encode($rendu);
	
	} // ...............  Fin de getBarVisualiser() ...............
		
	/**
	 * Liste les différents vainqueurs dans chaque localité
	 * @return string Objet JSON
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @param string $tour
	 */
	public function getWinnersLocalites($typeElection,$niveau,$params,$tour=''){
		$v=0;
		if ($niveau!="reg" && $niveau!="dep") return false;
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);

		//--------------------------
		$requeteTOTAL="SELECT ".self::attributLocalite($niveau).", SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		if ($niveau=="dep" OR $niveau=="reg")
			$requeteTOTAL.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg")
			$requeteTOTAL.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		
		for($i=0;$i<sizeof($params);$i++) {
			if( $colonnesBDD[$i] ){
				if ($v) $requeteTOTAL.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				else {$requeteTOTAL.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
			}
		}
		$requeteTOTAL.=" GROUP BY ".self::attributLocalite2($niveau)." ORDER BY ".self::attributLocalite2($niveau).",  nbVoix DESC";
		$resultatsTOTAL = $this->db->query($requeteTOTAL)->result();

		//--------------------------
		$v=0;
		$requete="SELECT rp.idCandidature, ".self::attributLocalite($niveau).", YEAR(dateElection) as annee, ";
		if ($this->isPresidentielle()) $requete.=" CONCAT(prenom, ' ', nom)";	else $requete.=" nomListe";
		$requete.="  as nomCandidat, ".self::nomLieu($niveau)." nomSource, SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		if ($niveau=="dep" OR $niveau=="reg")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
				
		for($i=0;$i<sizeof($params);$i++) {
			if( $colonnesBDD[$i] ){
				if ($v) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
			}
		}
		$requete.=" GROUP BY ".self::attributLocalite2($niveau).", `idCandidature` ORDER BY ".self::attributLocalite2($niveau).",  nbVoix DESC";
		$requete="SELECT idCandidature, nomCandidat, ".self::attributLocalite2($niveau)." as idLieu, nomLieu, nbVoix FROM ($requete) as TAB GROUP BY ".self::attributLocalite2($niveau)." HAVING  MAX(nbVoix)=nbVoix";
		
		$resultats = $this->db->query($requete)->result();
		
		$i=0;$data=array();
		
		foreach ($resultats as $resultat){
			if ($resultat->nomLieu!="ETRANGER")
				$data[]=array("name"=>$resultat->nomCandidat,"id"=>$resultat->idCandidature,"percent"=>(100*($resultat->nbVoix/$resultatsTOTAL[$i++]->nbVoix)), "voix"=>$resultat->nbVoix,"idLieu"=>$resultat->idLieu);
			else $i++;
		}
		echo json_encode($data);
						
	} // ............... getWinnerByRegion() ...............
	
	
	
	/**
	 * Diagramme circulaire des résultats
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 * @return string Objet JSON
	 */
	public function getPieVisualiser($typeElection,$niveau,$params){			
		$v=0;

		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
		
		if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)";
		else $requete.="nomListe";

		$requete.="  as nomCandidat, ".self::nomLieu($niveau)." nomSource, SUM( nbVoix ) as nbVoix
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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
		
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
		for($i=0;$i<sizeof($params);$i++) {
			if($v++){
				if ($colonnesBDD[$i]!=null) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}

		$requete.=" GROUP BY idCandidature";

		$resultats=$this->db->query($requete)->result();
		
		/* ----------------------------------------	*/
		/*			  TITRE DU DIAGRAMME			*/
		/* ----------------------------------------	*/

		list($this->titre,$this->sous_titre)=self::titre($resultats, $this->titreElection, $niveau);

		/* ----------------------------------------	*/
		/*			COLLECTE DES DONNEES			*/
		/* ----------------------------------------	*/

		$pieData=array();
		$i=0;
		
		foreach ($resultats as $resultat){
			$pieData[]=array("name"=>$resultat->nomCandidat,"y"=>(int)$resultat->nbVoix,"color"=>"{$this->colors[$i++]}",
			"url"=>"http://www.sigegis.ugb-edu.com/main_controller/getCandidat?id={$resultat->idCandidature}&typeElection={$typeElection}");
		}
						
		$rendu=array();
		$rendu[]=array("titre"=>$this->titre,"sous_titre"=>$this->sous_titre);		
		$rendu[]=array("type"=>"pie","name"=>$this->titre,"data"=>$pieData);
		
		echo json_encode($rendu);

	} // ...............  Fin de getPieVisualiser() ...............
		
	
	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	/**
	 * 
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 */
	 public function getGridVisualiser($typeElection, $niveau, $params){		
		
		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];
	
		if(!$sidx) $sidx =1;
		$v=0;
		
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);	
		
		$requeteTOTAL="SELECT SUM( nbVoix ) ";

		$joinPART=" FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$joinPART.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$joinPART.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$joinPART.=" LEFT JOIN pays ON region.idPays = pays.idPays";

		$requeteTOTAL.=$joinPART;
		$v=0;
		
		for($i=0;$i<sizeof($params);$i++) {
			if( $colonnesBDD[$i] ){
				if ($v) $wherePART.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				else {$wherePART=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
			}
		}
		
		$requeteTOTAL.=$wherePART;
		
		$requete="SELECT rp.idCandidature, ";
		if ($this->isPresidentielle()) $requete.=" CONCAT(prenom, ' ', nom)";	else $requete.=" nomListe";
		$requete.=" as nomCandidat,nomSource, SUM( nbVoix ) as nbVoix, (100*SUM( nbVoix )/($requeteTOTAL)) as pourcentage";
		$requete.=$joinPART.$wherePART;
		
		$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, ";
		if ($this->isPresidentielle()) $requeteCount.=" CONCAT(prenom, ' ', nom)";
		else $requeteCount.=" nomListe"; $requeteCount.=" as nomCandidat,nomSource
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN $this->tableCandidat ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre WHERE YEAR(election.dateElection)={$params[1]} AND election.typeElection='$typeElection'";
	
		$requeteCount.=" GROUP BY rp.idCandidature) as S";
	
		$count = $this->db->query($requeteCount)->result();
	
		$totalRows=$count[0]->total;
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;

		if($start <0) $start = 0;

		$requete.=" GROUP BY idCandidature ORDER BY $sidx $sord LIMIT $start,$limit";

		$resultats=$this->db->query($requete)->result();
	
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
		$s .= "<cell>". $row->nomSource ."</cell>";
		$s .= "</row>";
		}
		$s .= "</rows>";
		
		echo $s;
	} // ...............  Fin de getGrid() ...............
	
	/**
	 * Exportation des résultats
	 * @param string $typeElection
	 * @param string $niveau
	 * @param array $params
	 */
	public function exportResultatsToCSV($typeElection,$niveau,$params){
	
		$sord = $_GET['sord']; $v=0;
			
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);			
			
		$requeteTOTAL="SELECT SUM( nbVoix ) ";

		$joinPART=" FROM {$this->tables[$typeElection]} rp
		LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$joinPART.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$joinPART.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
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
		
		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, ";
		
		if ($this->isPresidentielle()) $requete.="CONCAT(prenom, ' ', nom)"; else $requete.="nomListe";
		
		$requete.=" as nomCandidat,nomSource, SUM( nbVoix ) as nbVoix, (100*SUM( nbVoix )/($requeteTOTAL)) as pourcentage";
		$requete.=$joinPART.$wherePART;
		
		$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, ";
		
		if ($this->isPresidentielle()) $requeteCount.="CONCAT(prenom, ' ', nom)";	else $requeteCount.="nomListe";
		
		$requeteCount.=" as nomCandidat,nomSource
		FROM {$this->tables[$typeElection]} rp
		LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre WHERE YEAR(election.dateElection)={$params[1]} AND election.typeElection='$typeElection'";

		$requete.=" GROUP BY idCandidature ORDER BY nbVoix $sord";

		$resultats=$this->db->query($requete)->result();

		header("Content-type: text/csv;charset=utf-8");
		header('Content-disposition: attachment;filename=SIGeGIS - Export.csv');
		$s="Nom du candidat;Voix;% exprimes;Source\r\n";
		
		foreach ($resultats as $row) {
			$s .= $row->nomCandidat .";";
			$s .= $row->nbVoix .";";
			$s .= $row->pourcentage.";";
			$s .= $row->nomSource;
			$s .= "\r\n";
		}
		
		echo $s;
	} // ............... exportResultatsToCSV() ...............	
	
			
	/**
	* Cette fonction affiche le code xml du Grid
	* @return string
	* @param string $typeElection
	* @param string $niveau
	* @param array $params
	* @param string $balise Le nom du conteneur Html
	*/
	public function getGridParticipation($typeElection,$niveau,$params){	
	
		$page = $_GET['page']; $limit = $_GET['rows']; $sidx = $_GET['sidx']; $sord = $_GET['sord'];
		$v=0;
		
		if(!$sidx) $sidx =1;
		
		$default="'Participation au niveau national' as nomLieu,";
		
		$requete="SELECT rp.idElection, typeElection, YEAR(dateElection) as annee, ".self::nomLieu($niveau,$default)." nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection		
		LEFT JOIN source ON rp.idSource = source.idSource	
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";				
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
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
		//$requete.=" GROUP BY rp.idElection";
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
	public function getComboParticipation($typeElection,$niveau,$params){
	
		$default="'Participation au niveau  national' as nomLieu,";	$v=0;
	
		$requete="SELECT rp.idElection, typeElection, YEAR(dateElection) as annee, ".self::nomLieu($niveau,$default)." nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
	
		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
		if (self::attributLocalite($niveau)) $colonnesBDD[]=self::attributLocalite($niveau);
		$colonnesBDD[]="election.typeElection";

		for($i=0;$i<sizeof($params);$i++) {
			if($v){
				if ($colonnesBDD[$i]) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
		}
		
		$resultats=$this->db->query($requete)->result();
		
		/* ----------------------------------------	*/
		/*			  TITRE DU DIAGRAMME			*/
		/* ----------------------------------------	*/

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
		
		/* ----------------------------------------	*/
		/*			COLLECTE DES DONNEES			*/
		/* ----------------------------------------	*/

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
	public function getPoidsElectoralRegions($typeElection,$niveau,$annee,$tour){
					
		$requete="SELECT nomRegion, YEAR(dateElection) as annee, SUM( nbInscrits ) as inscrits
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre 
		LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
		LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement 
		LEFT JOIN region ON departement.idRegion = region.idRegion 
		WHERE YEAR(dateElection)=$annee AND nomRegion<>'ETRANGER' AND election.tour='$tour' 
		GROUP BY region.idRegion ORDER BY nomRegion";
	
		$resultats=$this->db->query($requete)->result();
	
		/* ----------------------------------------	*/
		/*			  TITRE DU DIAGRAMME			*/
		/* ----------------------------------------	*/
		
		$defaultTitle="Poids électoral des régions";
		list($this->titre,$this->sous_titre)=self::titre($resultats, $this->titreElection, $niveau, $defaultTitle);

		/* ----------------------------------------	*/
		/*			COLLECTE DES DONNEES			*/
		/* ----------------------------------------	*/
	
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
	public function exportStatisticsToCSV($typeElection,$niveau,$params){

		$default="'Participation au niveau  national' as nomLieu,";	$v=0;

		$requete="SELECT rp.idElection,YEAR(dateElection) as annee, ".self::nomLieu($niveau,$default)." nomSource,sum(nbInscrits) as inscrits,sum(nbVotants) as votants,sum(nbBulletinsNuls) as nuls,sum(nbExprimes) as exprimes,(sum(nbInscrits)-sum(nbVotants)) as abstention
		FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";

		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays" OR $niveau=="globaux")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";

		$colonnesBDD[]="rp.idSource";
		$colonnesBDD[]="YEAR(election.dateElection)";
		if ($this->isPresidentielle()) $colonnesBDD[]="election.tour";
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

	/**
	 * Partie présentation du candidat
	 * @return string Objet JSON
	 */
	public function getCandidat(){
			$id=$_GET['id'];
			if (empty($id)) return ;
			$requete="SELECT * from $this->tableCandidat WHERE {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}=".mysql_real_escape_string($id);
			$resultats=$this->db->query($requete)->result();
			$s=array();
			foreach ($resultats as $row) {
				$s["idPhoto"]=$row->idCandidature;
				$s["prenom"]=$row->prenom;
				$s["nom"]=$row->nom;
				$date=explode("-", $row->dateNaissance);
				$s["dateNaissance"]=date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
				$s["lieuNaissance"]=$row->lieuNaissance;
				$s["contenu"]=$row->commentaires;
			}
			echo json_encode($s);
	} // ............... getCandidat() ...............
}