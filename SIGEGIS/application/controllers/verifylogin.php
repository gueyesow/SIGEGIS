<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model('user','',TRUE);
 }

 function index()
 {
   //This method will have the credentials validation
   $this->load->library('form_validation');

   $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

   if($this->form_validation->run() == FALSE)
   {
	   	$js_scripts["scripts_array"]=array("base","init_filtres","visualiser","tooltips","style");
	   	$top['title'] = 'SIGeGIS&gt;Vérification authentification';
	   	$top['styles'][] = 'theme';
	   	$data['head'] = $this->load->view('top',$top,true);
	   	$data['menu'] = $this->load->view('menu',$top,true);
	   	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	   	$data['footer'] = $this->load->view('footer',null,true);
	   	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	   	//Field validation failed.&nbsp; User redirected to login page
	   	$this->load->view('login_view',$data);
   }
   else
   {
     //Go to private area
     redirect('admin_controller', 'refresh');
   }

 }

 function check_database($password)
 {
   //Field validation succeeded.&nbsp; Validate against database
   $username = $this->input->post('username');

   //query the database
   $result = $this->user->login($username, $password);

   if($result)
   {
     $sess_array = array();
     foreach($result as $row)
     {
       $sess_array = array(
         'id' => $row->id,
         'username' => $row->username,
       	 'level' => $row->level
       );
       $this->session->set_userdata('logged_in', $sess_array);
     }
     return TRUE;
   }
   else
   {
     $this->form_validation->set_message('check_database', 'Identifiant ou mot de passe invalide');
     return false;
   }
 }
}
