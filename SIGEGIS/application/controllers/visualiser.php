<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur du module visualiser des résultats 
 * @author Amadou SOW & Abdou Khadre GUEYE
 *
 */
class Visualiser extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;
	
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
		$js_scripts["scripts_array"]=array("base.js","init_filtres.js","visualiser.js","style.js");
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
		$js_scripts["scripts_array"]=array("base.js","init_filtres.js","maps.js","jqsvg/jquery.svg.min.js","tooltips.js","style.js");
		$top['title'] = 'SIGeGIS>Cartographie';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('maps',$data);
	}
	
	public function apropos()
	{		
		$this->load->view('a_propos');
	}
		
	public function credits()
	{
		$js_scripts["scripts_array"]=array("base.js","credits.js","style.js");
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