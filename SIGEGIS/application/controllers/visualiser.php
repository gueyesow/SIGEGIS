<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur du module visualiser des résultats 
 * @author Amadou SOW & Abdou Khadre GUEYE
 *
 */
class Visualiser extends CI_Controller {		
	private $typeElection; // nom de l'election 
	private $niveau;	// niveau d'agregation des donnees 
	private $params; // autres parametres pouvant contenir les valeurs des listes deroulantes   
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("visualiser_model","visualizeModel");
		$this->load->helper('form');
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
	}

	public function index()
	{	
		$js_scripts["scripts_array"]=array("base","init_filtres","visualiser","style");
		$top['title'] = 'SIGeGIS, la plateforme pour les elections au Sénégal';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('front_page',$data);		
	}
	
	public function getMap()
	{
		$js_scripts["scripts_array"]=array("base","init_filtres","maps","jqsvg/jquery.svg.min","tooltips","style");
		$top['title'] = 'SIGeGIS>Cartographie';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('maps',$data);
	}
	
	public function exemples()
	{
		$js_scripts["scripts_array"]=array("base","init_filtres","exemples","tooltips","style");
		$top['title'] = 'SIGeGIS&gt;Exemples';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('exemples',$data);
	}
		
	public function credits()
	{
		$js_scripts["scripts_array"]=array("base","credits","style");
		$top['title'] = 'SIGeGIS';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('credits',$data);
	}
	
	public function resultats(){
		$this->index();
	}

	public function getWinnersLocalites(){
		$this->visualizeModel->getWinnersLocalites($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBar(){	
		echo $this->visualizeModel->getBar($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getPie(){				
		echo $this->visualizeModel->getPie($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGrid(){
		$this->visualizeModel->getGrid($this->typeElection,$this->niveau,$this->params);
	}
	
	public function exportResultatsToCSV(){
		$this->visualizeModel->exportResultatsToCSV($this->typeElection,$this->niveau,$this->params);
	}

	public function getFichePersonnelleCandidat(){
		$this->visualizeModel->getFichePersonnelleCandidat();
	}
	
}

/* End of file visualiser.php */
/* Location: ./application/controllers/visualiser.php */