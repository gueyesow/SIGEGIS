<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analyse_controller extends CI_Controller {

	function __construct(){
		// database et assets_helper sont charg�s automatiquement
		parent::__construct();
		$this->load->model("main_model","mon_modele");
		$this->load->model("analyse_model","modele_analyse");
		$this->load->model("filtering_model","mon_filtre");
		$this->load->helper('form');
	}

	public function index()
	{
		/*$data=array();
		 $data['histo']=$this->mon_modele->getHisto('chartdiv1',null);
		$data['pie']=$this->mon_modele->getPie('chartdiv2');
		$this->load->view('front_page',$data);*/
		$this->load->view('accueil');
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
		$data=array();
		$data['histo']=$this->mon_modele->getHisto('chartdiv1');
		$data['histo2']=$this->mon_modele->getHisto2('chartdiv1B');
		$data['pie']=$this->mon_modele->getPie('chartdiv2');
		$this->load->view('front_page',$data);
	}

	public function analyser(){
		$data=array();
		$data['histo']=$this->modele_analyse->getHisto('chartdiv1');
		$data['pie']=$this->mon_modele->getPie('chartdiv2');
		$this->load->view('analyses',$data);
	}

	public function administration(){
		$data=array();
		$this->load->view('admin_page',$data);
	}

	public function getHisto(){
		echo $this->mon_modele->getHisto("chartdiv1");
	}
	public function getHisto2(){
		echo $this->mon_modele->getHisto("chartdiv1B");
	}

	public function getPie(){
		echo $this->mon_modele->getPie("chartdiv2");
	}

	public function afficher(){
		$this->mon_modele->tableau("presidentielle2012");
	}

	public function search(){
		$this->mon_filtre->filtrer();
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

	public function filtre_grid(){
		$this->mon_modele->tableau("presidentielle2012");
	}
	public function getCandidatsAnnee(){
		$this->mon_filtre->getCandidatsAnnee();
	}

}

/* End of file main_controller.php */
/* Location: ./application/controllers/main_controller.php */