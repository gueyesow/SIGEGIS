<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_controller extends CI_Controller {
	
	function __construct(){
		// database et assets_helper sont charg�s automatiquement
		parent::__construct();
		$this->load->model("main_model","mon_modele");		
		$this->load->model("filtering_model","mon_filtre");
	}	
	/*
	class Admin extends Controller {
		//constructeur de la classe
		function Admin() {
			parent::Controller();
			$this->load->model('user_model');
		}
		function index(){
			if($this->user_model->isLoggedIn()){
				redirect('admin/dashboard','refresh');
			} else {
				redirect('admin/login','refresh');
			}
		}
		function login(){
			if($this->user_model->isLoggedIn()){
				redirect('admin','refresh');
			} else {
				//on charge la validation de formulaires
				$this->load->library('form_validation');
				//on définit les règles de succès
				$this->form_validation->set_rules('username','Login','required');
				$this->form_validation->set_rules('password','Mot de passe','required');
				//si la validation a échouée on redirige vers le formulaire de login
				if(!$this->form_validation->run()){
					$this->load->view('loginform');
				} else {
					$username = $this->input->post('username');
					$password = $this->input->post('password');
					$validCredentials = $this->user_model->validCredentials($username,$password);
					if($validCredentials){
						redirect('admin/dashboard','refresh');
					} else {
						$data['error_credentials'] = 'Wrong Username/Password';
						$this->load->view('loginform',$data);
					}
				}
			}
		}
		function dashboard(){
			if($this->user_model->isLoggedIn())
				$this->load->view('admin');
		}
	}
	*/
	public function index()
	{
		$data=array();
		/*$data['histoDateAmount']=$this->mon_modele->getDateAmountHisto('chartdiv1');
		$data['pieDateAmount']=$this->mon_modele->getDateAmountPie('chartdiv2');*/		
		$this->load->view('front_page',$data);
	}
	
	public function afficher(){
		$this->mon_modele->tableau();		
	}
	
	public function search(){		
		$this->mon_filtre->filtrer();
	}
	
	public function getDates(){
		$this->mon_filtre->getDates();
	}
	
	public function getSources(){
		$this->mon_filtre->getSources();
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
	
	public function filtre_grid(){
		$this->mon_filtre->tableau();
	}
	
}

/* End of file main_controller.php */
/* Location: ./application/controllers/main_controller.php */