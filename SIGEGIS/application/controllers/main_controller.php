<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_controller extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;
	
	private $tableCandidat;
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("main_model","basicModel");
		$this->load->model("analysis_model","analysisModel");
		$this->load->model("filtering_model","filteringModel");
		$this->load->helper('form');
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
	}

	public function index()
	{	
		$js_scripts["scripts_array"]=array("base","init_filtres","visualiser","tooltips","style");
		$top['title'] = 'SIGeGIS';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('front_page',$data);		
	}
	
	public function map()
	{
		$js_scripts["scripts_array"]=array("base","init_filtres","maps","jqsvg/jquery.svg.min","tooltips","style");
		$top['title'] = 'SIGeGIS>Map';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('maps',$data);
	}
	
	public function apropos()
	{		
		$this->load->view('a_propos');
	}
	
	public function exemples()
	{
		$js_scripts["scripts_array"]=array("base","init_filtres","exemples","tooltips","style");
		$top['title'] = 'SIGeGIS&gt;Exemples';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('exemples',$data);
	}
	
	public function visualiser(){
		$this->index();
	}

	public function analyser(){
		$js_scripts["scripts_array"]=array("base","init_filtres","updateFilters","analyses","dragAndDrop","tooltips","style");
		$top['title'] = 'SIGeGIS&gt;Analyses';
		$top['styles'] = array('theme','analyses');
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);		
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('analyses',$data);
	}
	
	public function participation(){	
		$js_scripts["scripts_array"]=array("base","init_filtres","participation","tooltips","style");
		$top['title'] = 'SIGeGIS&gt;Taux de participation';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('participation',$data);
	}
	
	public function getNomLocalite($idRegion="",$niveau=""){
		$this->filteringModel->getNomLocalite($idRegion,$niveau);
	}

	public function getWinnersLocalites(){
		$this->basicModel->getWinnersLocalites($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarVisualiser(){	
		echo $this->basicModel->getBarVisualiser($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getPieVisualiser(){				
		echo $this->basicModel->getPieVisualiser($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getComboParticipation(){
		echo $this->basicModel->getComboParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridVisualiser(){
		$this->basicModel->getGridVisualiser($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarAnalyserAnnee(){
		echo $this->analysisModel->getBarAnalyserAnnee($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarAnalyserLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			echo $this->analysisModel->getBarAnalyserLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats);
		} else return false;
	}
	
	public function getPoidsElectoralRegions(){
		if (!empty($_GET['annee']) AND !empty($_GET['tour'])) {
			$annee=$_GET['annee'];$tour=$_GET['tour'];
		} else return;
		echo $this->basicModel->getPoidsElectoralRegions($this->typeElection,$this->niveau,$annee,$tour);
	}
	
	public function getGridParticipation(){
		$this->basicModel->getGridParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridAnalyserAnnee(){
		$this->analysisModel->getGridAnalyserAnnee($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridAnalyserLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			$this->analysisModel->getGridAnalyserLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats);
		} else return false;	
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
	
	public function exportResultatsToCSV(){
		$this->basicModel->exportResultatsToCSV($this->typeElection,$this->niveau,$this->params);
	}
	public function exportStatisticsToCSV(){
		$this->basicModel->exportStatisticsToCSV($this->typeElection,$this->niveau,$this->params);
	}
	
	public function exportToCSVAnalyse(){
		$this->filteringModel->exportResultatsToCSV($this->typeElection,$this->niveau,$this->params);
	}
	
	public function exportToCSVAnalyseLocalite(){
		$this->filteringModel->exportToCSVLocalite();
	}	
	public function getCandidat(){
		$this->basicModel->getCandidat();
	}
}

/* End of file main_controller.php */
/* Location: ./application/controllers/main_controller.php */