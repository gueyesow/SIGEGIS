<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Traite toutes les informations relatives aux filtres
 * @author Amadou SOW & Abdou Khadre GUEYE
 *
 */
class Filtres_model extends CI_Model{
	
	private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales"); // tables des resultats
	private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales"); // tables des statistiques
	private $candidatOrListe=array("candidat"=>"idCandidat","listescoalitionspartis"=>"idListe"); // tables des candidats et des listes
	
	/**
	 * Retourne la liste des candidats suivant les parametres fournis.<br />
	 * <b>Partie:</b> Analyse suivant les années.
	 * @param string $typeElection le type de l'election en question
	 * @param string $niveau le niveau d'agregation des donnees
	 * @param array $params les autres parametres
	 * @param string $annees la liste des annees d'elections selectionnees par l'utilisateur (separees par des virgules)
	 * @param string $tableCandidat le nom de la table des candidats a la presidentielle ou celle des listes de partis
	 * @return string|boolean
	 */
	function getCandidatsAnnee($typeElection,$niveau,$params,$annees,$tableCandidat){
				
		if($annees){
			$arrayAnnees=explode(",",$annees);
		}

		if ( !empty($params) AND !empty($annees) AND !empty($niveau)) {
			
			$requete="SELECT rp.idCandidat, ";
			
			if($typeElection=="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			
			$requete.=" as nomCandidat
			FROM {$this->tables[$typeElection]} rp
			LEFT JOIN {$tableCandidat} ON rp.idCandidat = {$tableCandidat}.{$this->candidatOrListe[$tableCandidat]}
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
			
			$v=0;
			
			if ($niveau=="cen") $idLocalite="centre.idCentre";
			elseif ($niveau=="dep") $idLocalite="departement.idDepartement";
			elseif ($niveau=="reg") $idLocalite="region.idRegion";
			elseif ($niveau=="pays") $idLocalite="pays.idPays";
			else $idLocalite=null;
				
			$colonnesBDD[]="rp.idSource";
			if ($typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			if ($idLocalite) $colonnesBDD[]=$idLocalite;

			for($i=0;$i<sizeof($params);$i++) {
				if ($v)$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				else {$requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'"; $v++;}
			}
				
			for($i=0;$i<sizeof($arrayAnnees);$i++) {
				if ($i==0) $requete.=" AND (YEAR(election.dateElection) ='".$arrayAnnees[$i]."'";
				else $requete.=" OR YEAR(election.dateElection) ='".$arrayAnnees[$i]."'";
			}

			$requete.=") GROUP BY rp.idCandidat HAVING count(distinct rp.idElection)=".sizeOf($arrayAnnees);

			$query=$this->db->query($requete);

			$candidatures = array();

			if($query->result()){
				foreach ($query->result() as $candidat) {
					$candidatures[$candidat->idCandidat] = $candidat->nomCandidat;
				}
				echo json_encode($candidatures);
			}
			else // AUCUN RESULTAT (CANDIDAT)
			{
				$candidatures['0'] = "Aucun candidat";
				echo json_encode($candidatures);
			}
		} return FALSE;
	}

	/**
	 * Retourne la liste des candidats suivant les paramètres reçus.<br />
	 * <b>Partie:</b> Analyse suivant les localités.
	 * @param string $typeElection le type de l'election en question
	 * @param string $niveau le niveau d'agregation des donnees
	 * @param array $params les autres parametres
	 * @param string $localites la liste des localites (separees par des virgules)
	 * @param string $tableCandidat le nom de la table des candidats a la presidentielle ou celle des listes de partis
	 * @return string|boolean
	 */
	function getCandidatsLocalite($typeElection,$niveau,$params,$localites,$tableCandidat){
			
		if($localites){
			$arrayLocalites=explode(",",$localites);
		}

		if ( !empty($params) AND !empty($localites) AND !empty($niveau)) {
			
			$v=0;						
			
			$requete="SELECT rp.idCandidat, ";
			if($typeElection=="presidentielle") $requete.="CONCAT(prenom, ' ', nom)";
			else $requete.="nomListe";
			$requete.=" as nomCandidat
			FROM {$this->tables[$typeElection]} rp
			LEFT JOIN {$tableCandidat} ON rp.idCandidat = {$tableCandidat}.{$this->candidatOrListe[$tableCandidat]}
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

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			if ($typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			$colonnesBDD[]="YEAR(election.dateElection)";
			$colonnesBDD[]="election.typeElection";

			for($i=0;$i<sizeof($params);$i++) {
				if($v++)$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
				else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
				
			if ($niveau=="cen") $idLocalite="centre.idCentre";
			elseif ($niveau=="dep") $idLocalite="departement.idDepartement";
			elseif ($niveau=="reg") $idLocalite="region.idRegion";
			elseif ($niveau=="pays") $idLocalite="pays.idPays";
				
			for($i=0;$i<sizeof($arrayLocalites);$i++) {
				if(!$i) $requete.=" AND ($idLocalite ='".$arrayLocalites[$i]."'";
				else $requete.="OR $idLocalite ='".$arrayLocalites[$i]."'";
			}

			$requete.=") GROUP BY rp.idCandidat";

			$query=$this->db->query($requete);

			$candidatures = array();

			if($query->result()){
				foreach ($query->result() as $candidat) {
					$candidatures[$candidat->idCandidat] = $candidat->nomCandidat;
				}
				echo json_encode($candidatures);
			}
			else // AUCUN RESULTAT (CANDIDAT)
			{
				$candidatures['0'] = "Aucun candidat";
				echo json_encode($candidatures);
			}
		} return FALSE;
	}

	/**
	 * Retourne les annees d'elections ayant eu lieu suivant le même decoupage admnistratif
	 * @param string $typeElection le type de l'election en question
	 * @param int $anneeDecoupage l'anne du decoupage
	 * @return string|boolean
	 */
	function getDatesElections($typeElection,$anneeDecoupage){
		$requete="SELECT DISTINCT YEAR(dateElection) as annee FROM election";
		if(!empty($typeElection))
			$requete.=" WHERE typeElection='".$typeElection."'";
		
		if(!empty($anneeDecoupage))
			$requete.=" AND anneeDecoupage=".$anneeDecoupage;

		$requete.=" ORDER BY dateElection asc";
		$query=$this->db->query($requete);

		$elections = array();

		if($query->result()){
			foreach ($query->result() as $anneeElection) {
				$elections[$anneeElection->annee] = $anneeElection->annee;
			}
			echo json_encode($elections);
		}
		else{
			return FALSE;
		}
	}
	
	/**
	 * Retourne les annees d'elections
	 * @return string|boolean
	 */
	/*function getDatesElectionsAnalyse(){
		$requete="SELECT YEAR(dateElection) as annee FROM election";
		if(!empty($typeElection))
			$requete.=" WHERE typeElection='".$typeElection."'";
		
		if(!empty($anneeDecoupage))
			$requete.=" AND anneeDecoupage=".$anneeDecoupage;
	
		$requete.=" ORDER BY dateElection asc";
		$query=$this->db->query($requete);
	
		$elections = array();
	
		if($query->result()){
			foreach ($query->result() as $anneeElection) {
				$elections[$anneeElection->annee] = $anneeElection->annee;
			}
			echo json_encode($elections);
		}
		else
			return FALSE;
	}*/

	/**
	 * Retourne les centres d'une collectivite locale
	 * @param string $idCollectivite l'ID de la collectivite du centre en question
	 * @param int $anneeDecoupage annee decoupage
	 * @return string | boolean
	 */
	function getCentres($idCollectivite,$anneeDecoupage){
		$this->db->select('idCentre, nomCentre');
		$this->db->join('collectivite', 'centre.idCollectivite = collectivite.idCollectivite', 'left');
		$this->db->join('departement', 'collectivite.idDepartement = departement.idDepartement', 'left');
		$this->db->join('region', 'departement.idRegion = region.idRegion', 'left');
		$this->db->join('pays', 'region.idPays = pays.idPays', 'left');

		if($idCollectivite != NULL)	$this->db->where('collectivite.idCollectivite',$idCollectivite);

		if($anneeDecoupage) $this->db->where('anneeDecoupage', $anneeDecoupage);
		$query = $this->db->order_by('nomCentre')->get('centre');
		$centres = array();
		if($query->result()){
			foreach ($query->result() as $centre) {
				$centres[$centre->idCentre] = $centre->nomCentre;
			}
			echo json_encode($centres);
		}
		else
			return FALSE;
	}

	/**
	 * Retourne les collectivites d'un departement
	 * @param string $idDepartement l'ID du departement de la collectivite en question
	 * @param int $anneeDecoupage annee decoupage
	 * @return string | boolean
	 */
	function getCollectivites($idDepartement,$anneeDecoupage){
			
		$this->db->select('idCollectivite, nomCollectivite');
		$this->db->join('departement', 'collectivite.idDepartement = departement.idDepartement', 'left');
		$this->db->join('region', 'departement.idRegion = region.idRegion', 'left');
		$this->db->join('pays', 'region.idPays = pays.idPays', 'left');

		if($idDepartement != NULL) $this->db->where('departement.idDepartement', $idDepartement);

		if($anneeDecoupage) $this->db->where('anneeDecoupage', $anneeDecoupage);

		$query = $this->db->order_by('nomCollectivite')->get('collectivite');

		$collectivites = array();

		if($query->result()){
			foreach ($query->result() as $collectivite) {
				$collectivites[$collectivite->idCollectivite] = $collectivite->nomCollectivite;
			}
			echo json_encode($collectivites);
		}
		else
			return FALSE;
	}

	/**Retourne les departements d'une region
	 * 
	 * @param string $idRegion l'ID de la region du departement en question
	 * @param int $anneeDecoupage annee de decoupage
	 * @return boolean
	 */
	function getDepartements($idRegion,$anneeDecoupage){
		
		$this->db->select('idDepartement, nomDepartement');
		$this->db->join('region', 'departement.idRegion = region.idRegion', 'left');
		$this->db->join('pays', 'region.idPays = pays.idPays', 'left');

		if($idRegion != NULL) $this->db->where('region.idRegion', $idRegion);

		if($anneeDecoupage) $this->db->where('anneeDecoupage', $anneeDecoupage);

		$query = $this->db->order_by('nomDepartement')->get('departement'); // Table 'departement'

		$departements = array();

		if($query->result()){
			foreach ($query->result() as $departement)
			{
				if($departement->idDepartement != "D0")
					$departements[$departement->idDepartement] = $departement->nomDepartement;
			}
			echo json_encode($departements);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Retourne le nom du lieu de vote en question
	 * @param int|string $id
	 * @param string $niveau le niveau d'agregation
	 */
	function getNomLocalite($id,$niveau){	
		if ($id=="") echo  "Inconnue";
		if ($niveau=="dep") {$nomLieu="nomDepartement AS nomLieu";$idLieu="idDepartement";$table="departement";}
		elseif ($niveau=="reg") {$nomLieu="nomRegion AS nomLieu";$idLieu="idRegion";$table="region";}
		$this->db->select($nomLieu);		
		if($id)
			 $this->db->where($idLieu,$id);	
		$resultat = $this->db->get($table);
		foreach ($resultat->result() as $r) echo $r->nomLieu;
	}
	
	/**
	 * Retourne les regions suivant un decoupage administratif et le pays
	 * @param string $pays
	 * @param int $anneeDecoupage
	 * @return sting | boolean
	 */
	function getRegions($pays,$anneeDecoupage){

		$this->db->select('idRegion, nomRegion');
		$this->db->join('pays', 'region.idPays = pays.idPays', 'left');

		if($pays) $this->db->where('pays.idPays', $pays);

		if($anneeDecoupage) $this->db->where('anneeDecoupage', $anneeDecoupage);

		$query = $this->db->order_by('nomRegion')->get('region');

		$regions = array();

		if($query->result()){
			foreach ($query->result() as $region)
			{
				if($region->idRegion != "R0")
					$regions[$region->idRegion] = $region->nomRegion;
			}
			echo json_encode($regions);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Retourne les pays (Senagal ou Etranger)
	 * @param int $anneeDecoupage l'annee de decoupage
	 * @return string | boolean
	 */
	function getPays($anneeDecoupage){

		$this->db->select('idPays, nomPays');

		if($anneeDecoupage) $this->db->where('anneeDecoupage', $anneeDecoupage);		

		$query = $this->db->get('pays');
		
		$pays = array();

		if($query->result()){
			foreach ($query->result() as $lepays)
			{
				if($lepays->idPays != "0")
					$pays[$lepays->idPays] = $lepays->nomPays;
			}
			echo json_encode($pays);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Retourne les sources habilitees a suivre les elections
	 * @return string | boolean
	 */
	function getSources(){
		$this->db->select('idSource, nomSource');

		$query = $this->db->get('source'); // Table 'regions'

		$sources = array();

		if($query->result()){
			foreach ($query->result() as $source) {
				$sources[$source->idSource] = $source->nomSource;
			}
			echo json_encode($sources);
		}else{
			return FALSE;
		}
	}

	function getTours($dateElection){ // idElection => Tour
		$this->db->select('idElection,dateElection,tour');

		if(!empty($dateElection)) $this->db->where("YEAR(dateElection)",$dateElection);
		
		$query = $this->db->get('election'); // Table 'regions'

		$tours = array();

		if($query->result()){
			foreach ($query->result() as $tour) {
				if ($tour->tour=="premier_tour") $tours[$tour->tour] = "Premier tour";
				elseif ($tour->tour=="second_tour") $tours[$tour->tour] = "Second tour";
			}
			echo json_encode($tours);
		}else{
			return FALSE;
		}
	}

	/**
	 * Retourne les differents decoupages administratifs (Ex:2002,2008)
	 * @return String | boolean
	 */
	function getDecoupages(){ // idElection => Tour
		$requete="SELECT distinct anneeDecoupage FROM pays ORDER BY anneeDecoupage";
		$query=$this->db->query($requete);
		$decoupages = array();
		if($query->result()){
			foreach ($query->result() as $decoupage) {
				if($decoupage->anneeDecoupage) $decoupages[$decoupage->anneeDecoupage] = "Découpage de ".$decoupage->anneeDecoupage;
			}
			echo json_encode($decoupages);
		}
		else
			return FALSE;
	}
	
	/**
	 * Est utilisée par la carte (map.js)<br />
	 * Retourne l'année de découpage d'un lieu donné à partir de son ID
	 * @return int|boolean
	 */
	function getDecoupagePays($idPays){
		$requete="SELECT anneeDecoupage FROM pays WHERE idPays=$idPays";
		$query=$this->db->query($requete);
	
		if($query->num_rows()){
			foreach ($query->result() as $decoupage) {
				echo  $decoupage->anneeDecoupage;
			}
		}
		else return FALSE;
	}

}