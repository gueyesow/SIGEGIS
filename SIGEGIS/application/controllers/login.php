<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

 function __construct()
 {
   parent::__construct();
 }

 function index()
 {
 	$this->load->helper(array('form', 'url'));
 	$js_scripts["scripts_array"]=array("base","tooltips","style");
 	$top['title'] = 'SIGeGIS&gt;Connexion';
 	$top['styles'][] = 'theme';
 	$data['head'] = $this->load->view('top',$top,true);
 	$data['menu'] = $this->load->view('menu',$top,true);
 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
 	$data['footer'] = $this->load->view('footer',null,true);
 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
 	$this->load->view('login_view',$data); 	 
 }

}
