<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_controller extends CI_Controller {

	function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("main_model","mon_modele");
		$this->load->model("analyse_model","modele_analyse");
		$this->load->model("filtering_model","mon_filtre");
		$this->load->helper('form');
	}

	public function index()
	{		
		$this->load->view('front_page');
	}
	
	public function authentification()
	{
		$attributes = array('class' => 'email', 'id' => 'myform');

		echo form_open('main_controller/connexion', $attributes);

		$data = array(
				'name'        => 'login',
				'id'          => 'username',
				'maxlength'   => '100',
				'value'		=> 'Identifiant',
		);

		echo form_input($data);
		$data = array(
				'name'          => 'password',
				'value'          => 'password'
		);
		echo form_password($data);
		echo form_submit('mysubmit', 'Ok');
		$string = "</div></div>";
		echo form_close($string);

	}

	public function connexion(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('login', '"Nom d\'utilisateur"','trim|required|min_length[5]|max_length[52]|alpha_dash|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('password', '"Mot de passe"','trim|required|min_length[5]|max_length[52]|alpha_dash|encode_php_tags|xss_clean');
		if($this->form_validation->run())
		{
			// Le formulaire est valide
			$this->load->view('admin_page');
		}
		else
		{
			// Le formulaire est invalide ou vide
			$data=array("erreur"=>"1");
			$this->load->view('front_page',$data);
		}
	}

	public function visualiser(){
		$this->load->view('front_page');
	}

	public function analyser(){
		$this->load->view('analyses');
	}
	
	public function participation(){
		$this->load->view('participation');
	}

	public function administration(){
		$data=array();
		$this->load->view('admin_page',$data);
	}

	public function getBarVisualiser(){
		echo $this->mon_modele->getBarVisualiser();
	}
	
	public function getPieVisualiser(){
		echo $this->mon_modele->getPieVisualiser();
	}
	
	public function getComboParticipation(){
		echo $this->mon_modele->getComboParticipation();
	}
	
	public function getGridVisualiser(){
		$this->mon_modele->getGridVisualiser();
	}
	
	public function getBarAnalyserAnnee(){
		echo $this->modele_analyse->getBarAnalyserAnnee();
	}
	public function getPieAnalyserAnnee(){
		echo $this->modele_analyse->getPieAnalyserAnnee("chartdiv2");
	}
	
	public function getBarAnalyserLocalite(){
		echo $this->modele_analyse->getBarAnalyserLocalite();
	}
	
	public function getPieAnalyserLocalite(){
		echo $this->modele_analyse->getPieAnalyserLocalite();
	}

	public function getBarParticipation(){
		echo $this->mon_modele->getBarParticipation("chartdiv1");
	}
	
	public function getPieParticipation(){
		echo $this->mon_modele->getPieParticipation("chartdiv2");
	}
	public function getPieParticipation2(){
		echo $this->mon_modele->getPieParticipation2("chartdiv3");
	}
	
	public function getPoidsElectoralRegions(){
		echo $this->mon_modele->getPoidsElectoralRegions();
	}
	
	public function getGridParticipation(){
		$this->mon_modele->getGridParticipation();
	}

	public function getGridAnalyserAnnee(){
		$this->modele_analyse->getGridAnalyserAnnee();
	}
	
	public function getGridAnalyserLocalite(){
		$this->modele_analyse->getGridAnalyserLocalite();
	}
	
	public function getCandidats(){
		$this->mon_filtre->getCandidats();
	}

	public function getCandidatsArray(){
		$this->mon_filtre->getCandidatsArray();
	}

	public function getSources(){
		$this->mon_filtre->getSources();
	}

	public function getDatesElections(){
		$this->mon_filtre->getDatesElections();
	}

	public function getCentres(){
		$this->mon_filtre->getCentres();
	}

	public function getCollectivites(){
		$this->mon_filtre->getCollectivites();
	}

	public function getDepartements(){
		$this->mon_filtre->getDepartements();
	}

	public function getRegions(){
		$this->mon_filtre->getRegions();
	}

	public function getPays(){
		$this->mon_filtre->getPays();
	}

	public function getTours(){
		$this->mon_filtre->getTours();
	}

	public function getCandidatsAnnee(){
		$this->mon_filtre->getCandidatsAnnee();
	}
	
	public function getCandidatsLocalite(){
		$this->mon_filtre->getCandidatsLocalite();
	}
	
	public function getDecoupages(){
		$this->mon_filtre->getDecoupages();
	}
	
	public function exportResultatsToCSV(){
		$this->mon_modele->exportResultatsToCSV();
	}
	public function exportStatisticsToCSV(){
		$this->mon_modele->exportStatisticsToCSV();
	}
	
	public function exportToCSVAnalyse(){
		$this->mon_filtre->exportResultatsToCSV();
	}
	
	public function exportToCSVAnalyseLocalite(){
		$this->mon_filtre->exportToCSVLocalite();
	}
	public function test(){
		$this->load->view("clone");
	}		
}

/* End of file main_controller.php */
/* Location: ./application/controllers/main_controller.php */