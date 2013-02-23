<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur des filtres 
 * @author Amadou SOW & Abdou Khadre GUEYE 
 *
 */
class Filtres extends CI_Controller {		
	private $typeElection; // le type de l'election en question
	private $niveau;	// le niveau d'agregation des donnees
	private $params; // les autre parametres
	private $tableCandidat; // la table des candidats ou celle des listes de partis et coalitions de partis
	private $granularite; // Granularite des données d'origine
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("filtres_model","filtersModel");
		
		if(!empty($_GET["typeElection"]))	
			$this->typeElection=$_GET["typeElection"];	
		else 
			$this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	
		else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	
		else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
		
		if(!empty($_GET["typeElection"])) {
			
			if ($this->typeElection=="presidentielle") 
				$this->tableCandidat="candidat";
			else 
				$this->tableCandidat="listescoalitionspartis";
			
			if ($this->typeElection=="presidentielle") $this->titreElection="présidentielle";
			elseif ($this->typeElection=="legislative") $this->titreElection="législative";
			elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
			else $this->titreElection=$this->typeElection;
		}
		
		if(!empty($_GET['g'])) $this->granularite=$_GET['g'];else $this->granularite="centre";
	}
	
	public function getNomLocalite($idRegion="",$niveau=""){
		$this->filtersModel->getNomLocalite($idRegion,$niveau);
	}

	public function getSources(){
		$this->filtersModel->getSources();
	}

	// Fournit les annees d'election suivant les parametres fournis
	public function getDatesElections(){
		$anneeDecoupage=NULL;
		
		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		
		$this->filtersModel->getDatesElections($this->typeElection,$anneeDecoupage);
	}

	// Fournit la liste des centres suivant les parametres fournis
	public function getCentres(){
		$idCollectivite = NULL;
		$anneeDecoupage=NULL;
		
		if (!empty($_GET["idCollectivite"])) $idCollectivite=$_GET["idCollectivite"];
		
		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		
		$this->filtersModel->getCentres($idCollectivite,$anneeDecoupage);
	}

	// Fournit la liste des collectivites suivant les parametres fournis
	public function getCollectivites(){
		$idDepartement = NULL;
		$anneeDecoupage=NULL;
		
		if (!empty($_GET["idDepartement"])) $idDepartement=$_GET["idDepartement"];
		
		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		
		$this->filtersModel->getCollectivites($idDepartement,$anneeDecoupage);
	}

	// Fournit la liste des departements suivant les parametres fournis
	public function getDepartements(){
		$idRegion = NULL;
		$anneeDecoupage=NULL;
		
		if (!empty($_GET["idRegion"])) $idRegion=$_GET["idRegion"];
		
		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		
		$this->filtersModel->getDepartements($idRegion,$anneeDecoupage);
	}

	// Fournit la liste des regions suivant les parametres fournis
	public function getRegions(){
		$pays = NULL;
		$anneeDecoupage=NULL;
		if (!empty($_GET["idPays"])) $pays=$_GET["idPays"];
		
		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		$this->filtersModel->getRegions($pays,$anneeDecoupage);
	}

	// Fournit la liste des pays suivant les parametres fournis
	public function getPays(){

		if (!empty($_GET["anneeDecoupage"])) $anneeDecoupage=$_GET["anneeDecoupage"];
		
		else {
			$queryAnneesDecoupage=$this->db->query("SELECT distinct anneeDecoupage FROM election ORDER BY anneeDecoupage")->result();
			foreach ($queryAnneesDecoupage as $b)
				$anneesDecoupage[]=$b->anneeDecoupage."<br>";
			foreach ($anneesDecoupage as $decoupage){
				if ($_GET["paramAnnee"]>=$decoupage) $anneeDecoupage=$decoupage;
				else break;
			}
		}

		$this->filtersModel->getPays($anneeDecoupage);
	}

	// Retourne les tours d'une election presidentielle
	public function getTours(){
		$dateElection=$_GET["dateElection"];
		$this->filtersModel->getTours($dateElection);
	}

	// Retourne la liste des candidats suivant les parametres fournis
	public function getCandidatsAnnee(){
		$annees=$_GET["annees"];
		$this->filtersModel->getCandidatsAnnee($this->typeElection,$this->niveau,$this->params,$this->granularite,$annees,$this->tableCandidat);
	}
	
	// Retourne la liste des candidats suivant les parametres fournis
	public function getCandidatsLocalite(){
		$localites=$_GET["localites"];
		$this->filtersModel->getCandidatsLocalite($this->typeElection,$this->niveau,$this->params,$this->granularite,$localites,$this->tableCandidat);
	}
	
	// Retourne les differents decoupages administratifs
	public function getDecoupages(){
		$this->filtersModel->getDecoupages();
	}
	
	public function getDecoupagePays(){
		$idPays=!empty($_GET["idPays"])?mysql_real_escape_string($_GET["idPays"]):null;
		if ($idPays)
		$this->filtersModel->getDecoupagePays($idPays);
	}
	
}

/* End of file filtres.php */
/* Location: ./application/controllers/filtres.php */