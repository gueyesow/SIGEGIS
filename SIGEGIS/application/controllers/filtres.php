<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Filtres extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;
	
	private $tableCandidat;
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("filtres_model","filteringModel");
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
	}
	
	public function getNomLocalite($idRegion="",$niveau=""){
		$this->filteringModel->getNomLocalite($idRegion,$niveau);
	}

	public function getCandidats(){
		$this->filteringModel->getCandidats();
	}

	public function getCandidatsArray(){
		$this->filteringModel->getCandidatsArray();
	}

	public function getSources(){
		$this->filteringModel->getSources();
	}

	public function getDatesElections(){
		$this->filteringModel->getDatesElections();
	}

	public function getCentres(){
		$this->filteringModel->getCentres();
	}

	public function getCollectivites(){
		$this->filteringModel->getCollectivites();
	}

	public function getDepartements(){
		$this->filteringModel->getDepartements();
	}

	public function getRegions(){
		$this->filteringModel->getRegions();
	}

	public function getPays(){
		$this->filteringModel->getPays();
	}

	public function getTours(){
		$this->filteringModel->getTours();
	}

	public function getCandidatsAnnee(){
		$this->filteringModel->getCandidatsAnnee();
	}
	
	public function getCandidatsLocalite(){
		$this->filteringModel->getCandidatsLocalite();
	}
	
	public function getDecoupages(){
		$this->filteringModel->getDecoupages();
	}
	
	public function getDecoupagePays(){
		$this->filteringModel->getDecoupagePays();
	}
	
}

/* End of file filtres.php */
/* Location: ./application/controllers/filtres.php */