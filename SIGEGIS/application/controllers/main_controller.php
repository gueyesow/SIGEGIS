<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_controller extends CI_Controller {

	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("main_model","basicModel");
		$this->load->model("analysis_model","analysisModel");
		$this->load->model("filtering_model","filteringModel");
		$this->load->helper('form');
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
	
	public function apropos()
	{		
		$this->load->view('a_propos');
	}
	
	public function exemples()
	{
		$this->load->view('exemples');
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

	public function getBarVisualiser(){
		echo $this->basicModel->getBarVisualiser();
	}
	
	public function getPieVisualiser(){
		echo $this->basicModel->getPieVisualiser();
	}
	
	public function getComboParticipation(){
		echo $this->basicModel->getComboParticipation();
	}
	
	public function getGridVisualiser(){
		$this->basicModel->getGridVisualiser();
	}
	
	public function getBarAnalyserAnnee(){
		echo $this->analysisModel->getBarAnalyserAnnee();
	}
	public function getPieAnalyserAnnee(){
		echo $this->analysisModel->getPieAnalyserAnnee("chartdiv2");
	}
	
	public function getBarAnalyserLocalite(){
		echo $this->analysisModel->getBarAnalyserLocalite();
	}
	
	public function getPieAnalyserLocalite(){
		echo $this->analysisModel->getPieAnalyserLocalite();
	}

	public function getBarParticipation(){
		echo $this->basicModel->getBarParticipation("chartdiv1");
	}
	
	public function getPieParticipation(){
		echo $this->basicModel->getPieParticipation("chartdiv2");
	}
	public function getPieParticipation2(){
		echo $this->basicModel->getPieParticipation2("chartdiv3");
	}
	
	public function getPoidsElectoralRegions(){
		echo $this->basicModel->getPoidsElectoralRegions();
	}
	
	public function getGridParticipation(){
		$this->basicModel->getGridParticipation();
	}

	public function getGridAnalyserAnnee(){
		$this->analysisModel->getGridAnalyserAnnee();
	}
	
	public function getGridAnalyserLocalite(){
		$this->analysisModel->getGridAnalyserLocalite();
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
	
	public function exportResultatsToCSV(){
		$this->basicModel->exportResultatsToCSV();
	}
	public function exportStatisticsToCSV(){
		$this->basicModel->exportStatisticsToCSV();
	}
	
	public function exportToCSVAnalyse(){
		$this->filteringModel->exportResultatsToCSV();
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