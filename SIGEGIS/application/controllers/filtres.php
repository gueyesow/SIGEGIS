<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur des filtres 
 * @author Amadou SOW & Abdou Khadre GUEYE 
 *
 */
class Filtres extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("filtres_model","filtersModel");
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
	}
	
	public function getNomLocalite($idRegion="",$niveau=""){
		$this->filtersModel->getNomLocalite($idRegion,$niveau);
	}

	public function getCandidats(){
		$this->filtersModel->getCandidats();
	}

	public function getCandidatsArray(){
		$this->filtersModel->getCandidatsArray();
	}

	public function getSources(){
		$this->filtersModel->getSources();
	}

	public function getDatesElections(){
		$this->filtersModel->getDatesElections();
	}

	public function getCentres(){
		$this->filtersModel->getCentres();
	}

	public function getCollectivites(){
		$this->filtersModel->getCollectivites();
	}

	public function getDepartements(){
		$this->filtersModel->getDepartements();
	}

	public function getRegions(){
		$this->filtersModel->getRegions();
	}

	public function getPays(){
		$this->filtersModel->getPays();
	}

	public function getTours(){
		$this->filtersModel->getTours();
	}

	public function getCandidatsAnnee(){
		$this->filtersModel->getCandidatsAnnee();
	}
	
	public function getCandidatsLocalite(){
		$this->filtersModel->getCandidatsLocalite();
	}
	
	public function getDecoupages(){
		$this->filtersModel->getDecoupages();
	}
	
	public function getDecoupagePays(){
		$this->filtersModel->getDecoupagePays();
	}
	
}

/* End of file filtres.php */
/* Location: ./application/controllers/filtres.php */