<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_controller extends CI_Controller {	
	
	function __construct(){
		// database et assets_helper sont charg�s automatiquement
		parent::__construct();
		$this->load->model("main_model","mon_modele");
		$this->load->model("filtering_model","mon_filtre");
	}
	
	function index(){
		if($this->user_model->isLoggedIn()){
			redirect('admin/dashboard','refresh');
		} 
		else {
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
}

/* End of file main_controller.php */
/* Location: ./application/controllers/main_controller.php */