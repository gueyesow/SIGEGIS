<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

session_start(); // Ouverture d'une session

class Admin extends CI_Controller {
	private $typeElection;
	private $niveau;
	private $params;
	private $la_session;
	private $typeLocalite;
	private $repertoire; // Répertoire ou une image va etre stockee (photos, logos, cartes,...)

	function __construct()
	{
	   parent::__construct();
	   $this->load->model("visualiser_model","visualizeModel");
	   $this->load->model("admin_model","adminModel");
	   $this->load->model("filtres_model","filtersModel");
	   $this->load->helper('form','ckeditor','url');
	   $this->load->model('user','',TRUE);
	   
	   if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
	   
	   if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
	   
	   if(!empty($_GET['param'])) {
	   	$parametres=$_GET['param'];
	   }	else $parametres="1,2012,premier_tour,globaux";
	   
	   $this->params=explode(",",$parametres);
	   
	   if(!empty($_GET["typeLocalite"])) $this->typeLocalite=$_GET["typeLocalite"];
	   $this->la_session=$this->session->userdata('logged_in');
	}

	function index()
	{
		if($this->session->userdata('logged_in'))
		{
	   	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","admin","style");
	   	$top['title'] = 'SIGeGIS&gt;Administration';
	   	$top['styles'][] = 'theme';
	   	$data['head'] = $this->load->view('top',$top,true);
	   	$data['menu'] = $this->load->view('menu',$top,true);
	   	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	   	$data['footer'] = $this->load->view('footer',null,true);
	   	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	    $session_data = $this->session->userdata('logged_in');
	    $data['username'] = $session_data['username'];
	    $this->load->view('admin_page', $data);
		}
		else
		{
			//If no session, redirect to login page
			redirect('admin/login', 'refresh');
		}
	}

	public function editResultats()
	{
		$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","admin","style");
	 	$top['title'] = 'SIGeGIS&gt;Modifier les résultats d\'une élection';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editResultats', $data); 
	 }
	 
	 public function editParticipations()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","adminParticipations","style");
	 	$top['title'] = 'SIGeGIS&gt;Modifier taux de participation';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editParticipations', $data);
	 }
	 
	 public function editElections()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","adminElections","style");
	 	$top['title'] = 'SIGeGIS&gt;Elections';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editElections', $data);
	 }
	
	 public function editSources()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","adminSources","style");
	 	$top['title'] = 'SIGeGIS&gt;Sources';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editSources', $data);
	 }
	 
	 public function editUsers()
	 {
	 	$js_scripts["scripts_array"]=array("base","adminUsers","style");
	 	$top['title'] = 'SIGeGIS&gt;Utilisateurs';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editUsers', $data);
	 }
	 
	 public function editCandidats()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","adminCandidats","ckeditor/ckeditor","ckeditor/adapters/jquery","style");
	 	$top['title'] = 'SIGeGIS&gt;Candidats';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editCandidats', $data);
	 }
	 
	 public function editListes()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","adminListes","ckeditor/ckeditor","ckeditor/adapters/jquery","style");
	 	$top['title'] = 'SIGeGIS&gt;Listes de partis et de coalitions';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editListes', $data);
	 }
	 
	 public function editLocalites()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","adminLocalites","style");
	 	$top['title'] = 'SIGeGIS&gt;Modifier les localités';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/editLocalites', $data);
	 }
	 
	 public function getGridResultats()
	 {
	 	$this->adminModel->getGridResultats($this->typeElection,$this->niveau,$this->params);
	 }
	 
	 public function getGridParticipation()
	 {
	 	$this->adminModel->getGridParticipation($this->typeElection,$this->niveau,$this->params);
	 }
	 
	 public function getGridElections()
	 {
	 	$this->adminModel->getGridElections($this->typeElection,$this->niveau,$this->params);
	 }
	 
	 public function getGridUsers()
	 {
	 	$this->adminModel->getGridUsers($this->typeElection,$this->niveau,$this->params);
	 }
	 
	 public function getGridSources()
	 {
	 	$this->adminModel->getGridSources($this->typeElection,$this->niveau,$this->params);
	 }
	
	 public function getGridLocalites()
	 {
	 	$annee = $_GET['annee'];
	 	$this->adminModel->getGridLocalites($this->typeElection,$this->niveau,$this->params,$this->typeLocalite,$annee);
	 }
	 
	 public function getGridCandidats()
	 {
	 	$annee= $_GET['annee'];
	 	$this->adminModel->getGridCandidats($this->typeElection,$this->niveau,$this->params,$annee);
	 }
	 
	 public function getGridCoalitionsPartis()
	 {
	 	$annee= mysql_real_escape_string($_GET['annee']);
	 	$this->adminModel->getGridCoalitionsPartis($this->typeElection,$this->niveau,$this->params,$annee);
	 }
	 
	 public function resultatCRUD()
	 {
	 	$this->adminModel->resultatCRUD($this->la_session);
	 }
	 
	 public function participationCRUD()
	 {
	 	$this->adminModel->participationCRUD($this->la_session);
	 }
	 
	 public function electionCRUD()
	 {
	 	$this->adminModel->electionCRUD($this->la_session);
	 }
	 
	 public function sourceCRUD()
	 {
	 	$this->adminModel->sourceCRUD($this->la_session);
	 }
	 
	 public function userCRUD()
	 {
	 	$this->adminModel->userCRUD($this->la_session);
	 }
	 
	 public function localiteCRUD()
	 {
	 	$this->adminModel->localiteCRUD($this->la_session);
	 }
	 
	 public function listeCRUD()
	 {
	 	$this->adminModel->listeCRUD($this->la_session);
	 }
	 
	 public function candidatCRUD()
	 {
	 	$this->adminModel->candidatCRUD($this->la_session);
	 }
	 
	 /**
	  * Page du formulaire d'authentification
	  */
	 function login()
	 {
	 	$js_scripts["scripts_array"]=array("base","style");
	 	$top['title'] = 'SIGeGIS&gt;Connexion';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('login_view',$data);
	 }
	 
	 function verifylogin()
	 {
	 	$this->load->library('form_validation');
	 
	 	$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
	 	$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
	 
	 	if($this->form_validation->run() == FALSE)
	 	{
	 		$js_scripts["scripts_array"]=array("base","style");
	 		$top['title'] = 'SIGeGIS&gt;Vérification authentification';
	 		$top['styles'][] = 'theme';
	 		$data['head'] = $this->load->view('top',$top,true);
	 		$data['menu'] = $this->load->view('menu',$top,true);
	 		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 		$data['footer'] = $this->load->view('footer',null,true);
	 		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 		$this->load->view('login_view',$data);
	 	}
	 	else
	 	{
	 		//Redirection vers la page d'administration en cas d'echec de la validation du formulaire
	 		redirect('admin', 'refresh');
	 	}
	 
	 }
	 
	 /**
	  * Vérification dans la BDD des identifiants fournis en utilisant le modele User
	  * @param string $password
	  * @return boolean
	  */
	 function check_database($password)
	 {
	 	//formulaire validé 
	 	$username = $this->input->post('username');
	 
	 	//verifie dans la base de données
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
	 
	 public function logout()
	 {
	 	$this->session->unset_userdata('logged_in');
	 	session_destroy();
	 	redirect('visualiser', 'refresh');
	 }
	 
	 function upload()
	 {
	 	$js_scripts["scripts_array"]=array("base","style");
	 	$top['title'] = 'SIGeGIS>Uploder des images';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$data['error'] = ' ';
	 	$this->load->view('admin/upload_form',$data);
	 }
	 
	 function do_upload()
	 {
	 	if(!empty($_POST["repertoire"]))	{
	 		$this->repertoire=$_POST["repertoire"];
	 		if(!$_FILES['userfile']['size']) redirect("admin/upload");
	 	} else redirect("admin");
	 
	 	$config['upload_path'] = dirname(BASEPATH)."/assets/images/{$this->repertoire}";
	 	$config['allowed_types'] = 'jpg|png|gif|svg|xml';
	 	$config['overwrite'] = TRUE;
	 	$config['max_size'] = 1024 * 8;
	 
	 	$this->load->library('upload', $config);
	 
	 	if ( ! $this->upload->do_upload())
	 	{
	 		$error = array('error' => $this->upload->display_errors());
	 
	 		$this->load->view('admin/upload_form', $error);
	 	}
	 	else
	 	{
	 		$data = array('upload_data' => $this->upload->data());
	 
	 		$js_scripts["scripts_array"]=array("base","style");
	 		$top['title'] = 'SIGeGIS>Upload effectué avec succès';
	 		$top['styles'][] = 'theme';
	 		$data['head'] = $this->load->view('top',$top,true);
	 		$data['menu'] = $this->load->view('menu',$top,true);
	 		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 		$data['footer'] = $this->load->view('footer',null,true);
	 		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 		$data['error'] = ' ';
	 		$this->load->view('admin/upload_success', $data);
	 	}
	 }
	 
	 function delete(){
	 	$rep=$_GET["rep"];
	 	$file=$_GET["file"];
	 	unlink(dirname(BASEPATH)."/assets/images/$rep/$file");
	 }
	 
	 function ScanDirectory($Directory=""){
	 	$save=$Directory;$i=1;
	 	$Directory=dirname(BASEPATH)."/assets/images/$Directory";
	 	$MyDirectory = opendir($Directory) or die('Erreur');
	 	$output="";
	 	while($Entry = @readdir($MyDirectory)) {
	 
	 		if ($Entry != '.' && $Entry != '..'){
	 			if(is_dir($Directory.'/'.$Entry)) {
	 				$output.='<ul>'.$Directory;
	 				ScanDirectory($Directory.'/'.$Entry);
	 				$output.='</ul>';
	 			}
	 			else {
	 				$output.="<li><button class='unlink'>&nbsp;</button>&nbsp;".($i++)."<a href='".base_url()."assets/images/$save/$Entry'>".$Entry."</a></li>";
	 			}
	 		}
	 	}
	 	closedir($MyDirectory);
	 	echo  $output;
	 }
	 
	 /**
	  * Cette méthode n'est pas complète | EN CHANTIER !!!
	  */
	 public function importer()
	 {
	 	$js_scripts["scripts_array"]=array("base","datepicker-fr","init_filtres","style");
	 	$top['title'] = 'SIGeGIS&gt;Importer';
	 	$top['styles'][] = 'theme';
	 	$data['head'] = $this->load->view('top',$top,true);
	 	$data['menu'] = $this->load->view('menu',$top,true);
	 	$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
	 	$data['footer'] = $this->load->view('footer',null,true);
	 	$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
	 	$this->load->view('admin/importer', $data);
	 }
}

?>
