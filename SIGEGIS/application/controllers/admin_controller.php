<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Admin_controller extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model("main_model","basicModel");
   $this->load->model("admin_model","adminModel");
   $this->load->model("filtering_model","filteringModel");
   $this->load->helper('form');
 }

 function index()
 {
   if($this->session->userdata('logged_in'))
   {
   	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","admin","tooltips","style");
   	$top['title'] = 'SIGeGIS&gt;Connexion';
   	$top['styles'][] = 'theme';
   	$data['head'] = $this->load->view('top',$top,true);
   	$data['menu'] = $this->load->view('menu',$top,true);
   	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
   	$data['footer'] = $this->load->view('footer',null,true);
   	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
    $session_data = $this->session->userdata('logged_in');
    $data['username'] = $session_data['username'];
    $this->load->helper('form');
    $this->load->view('admin_page', $data);     
   }
   else
   {
   	$this->load->helper('form');
     //If no session, redirect to login page
     redirect('login', 'refresh');
   }
 }

 public function logout()
 {
   $this->session->unset_userdata('logged_in');
   session_destroy();
   redirect('main_controller', 'refresh');
 }
 
 public function editLocalites()
 {
 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","admin","tooltips","style");
 	$top['title'] = 'SIGeGIS&gt;Modifier une localité';
 	$top['styles'][] = 'theme';
 	$data['head'] = $this->load->view('top',$top,true);
 	$data['menu'] = $this->load->view('menu',$top,true);
 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
 	$data['footer'] = $this->load->view('footer',null,true);
 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
 	$this->load->view('admin/editLocalites', $data); 
 }
 
 public function updateGrid()
 {
 	$this->adminModel->getGridVisualiser();
 }
 
 public function editRP()
 {
 	$this->adminModel->editRP();
 }

}

?>
