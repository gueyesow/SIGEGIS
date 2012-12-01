<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analyser extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("analyser_model","analysisModel");
		$this->load->helper('form');
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
	}
		
	public function index(){
		$js_scripts["scripts_array"]=array("base.js","init_filtres.js","analyses.js","dragAndDrop.js","updateFilters.js","style.js");
		$top['title'] = 'SIGeGIS&gt;Analyses';
		$top['styles'] = array('theme','analyses');
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);		
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('analyses',$data);
	}
	
	public function exportStatisticsToCSV(){
		$this->analysisModel->exportStatisticsToCSV($this->typeElection,$this->niveau,$this->params);
	}
	
	public function participation(){	
		$js_scripts["scripts_array"]=array("base.js","init_filtres.js","participation.js","style.js");
		$top['title'] = 'SIGeGIS&gt;Taux de participation';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('participation',$data);
	}
		
	public function getComboParticipation(){
		echo $this->analysisModel->getComboParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarAnalyserSuivantAnnee(){
		echo $this->analysisModel->getBarAnalyserSuivantAnnee($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarAnalyserSuivantLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			echo $this->analysisModel->getBarAnalyserSuivantLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats);
		} else return false;
	}
	
	public function getPoidsElectoralRegions(){
		if (!empty($_GET['annee']) AND !empty($_GET['tour'])) {
			$annee=$_GET['annee'];$tour=$_GET['tour'];
		} else return;
		echo $this->analysisModel->getPoidsElectoralRegions($this->typeElection,$this->niveau,$annee,$tour);
	}
	
	public function getGridParticipation(){
		$this->analysisModel->getGridParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridAnalyserSuivantAnnee(){
		$this->analysisModel->getGridAnalyserSuivantAnnee($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridAnalyserSuivantLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			$this->analysisModel->getGridAnalyserSuivantLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats);
		} else return false;	
	}
}

/* End of file analyser.php */
/* Location: ./application/controllers/analyser.php */