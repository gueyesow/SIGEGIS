<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contrôleur du module visualiser des résultats
 * @author Amadou SOW & Abdou Khadre GUEYE
 *
 */
class Analyser extends CI_Controller {		
	private $typeElection;
	private $niveau;	
	private $params;	
	private $tableCandidat;
	
	public function __construct(){
		// database et assets_helper sont chargés automatiquement
		parent::__construct();
		$this->load->model("analyser_model","analysisModel");
		$this->load->helper('form');
		
		if(!empty($_GET["typeElection"]))	$this->typeElection=$_GET["typeElection"];	else $this->typeElection=null;
		
		if(!empty($_GET["niveau"]))	$this->niveau=$_GET["niveau"];	else $this->niveau=null;
					
		if(!empty($_GET['param'])) {$parametres=$_GET['param'];}	else $parametres="1,2012,premier_tour,globaux";

		$this->params=explode(",",$parametres);
		
		
		if ($this->typeElection=="presidentielle") $this->tableCandidat="candidat";else $this->tableCandidat="listescoalitionspartis";
			
	}
	
	/**
	 * Teste si l'on a affaire à une élection présidentielle
	 */
	public function isPresidentielle(){
		return ($this->typeElection=="presidentielle")?true:false;
	}
	
	// Page où l'utilisateur va exécuter ses requêtes		
	public function index(){
		$js_scripts["scripts_array"]=array("base","init_filtres","analyses","dragAndDrop","updateFilters","style");
		$top['title'] = 'SIGeGIS&gt;Analyses';
		$top['styles'] = array('theme','analyses');
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);		
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('analyses',$data);
	}

	// Exportation des informations sur la participation	
	public function exportStatisticsToCSV(){
		$this->analysisModel->exportStatisticsToCSV($this->typeElection,$this->niveau,$this->params);
	}
	
	// Page de visualisation des taux de participation
	public function participation(){	
		$js_scripts["scripts_array"]=array("base","init_filtres","participation","style");
		$top['title'] = 'SIGeGIS&gt;Taux de participation';
		$top['styles'][] = 'theme';
		$data['head'] = $this->load->view('top',$top,true);
		$data['menu'] = $this->load->view('menu',$top,true);
		$data['options_menu'] = $this->load->view('menu_des_options',$top,true);
		$data['footer'] = $this->load->view('footer',null,true);
		$data['scripts'] = $this->load->view('bottom',$js_scripts,true);
		$this->load->view('participation',$data);
	}

	/**
	 * Retourne les données pour le graphique représentant les informations sur la participation.<br />
	 * Ce graphique est composé de plusieurs diagrammes.
	 */	
	public function getComboParticipation(){
		echo $this->analysisModel->getComboParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getBarAnalyserSuivantAnnee(){		
		if (!empty($_GET["listeAnnees"]) && !empty($_GET["listeCandidats"])){
			$listeAnnees=explode(",",$_GET["listeAnnees"]);
			$listeCandidats=explode(",",$_GET["listeCandidats"]);
			echo $this->analysisModel->getBarAnalyserSuivantAnnee($this->typeElection,$this->niveau,$this->params,$listeAnnees,$listeCandidats,$this->tableCandidat);
		}		
		else return FALSE;
	}
	
	public function getBarAnalyserSuivantLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			echo $this->analysisModel->getBarAnalyserSuivantLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats,$this->tableCandidat);
		} else return false;
	}
	
	public function getPoidsElectoralRegions(){
		if (!empty($_GET['annee']) AND !empty($_GET['tour'])) {
			$annee=$_GET['annee'];$tour=$_GET['tour'];
		} else return;
		echo $this->analysisModel->getPoidsElectoralRegions($this->typeElection,$this->niveau,$annee,$tour);
	}
	
	public function getGridParticipation(){
		$this->analysisModel->getGridParticipation($this->typeElection,$this->niveau,$this->params);
	}
	
	public function getGridAnalyserSuivantAnnee(){		
		if (!empty($_GET["listeAnnees"]) AND !empty($_GET["listeCandidats"])){
			$listeAnnees=$_GET["listeAnnees"];
			$listeCandidats=$_GET["listeCandidats"];
			$this->analysisModel->getGridAnalyserSuivantAnnee($this->typeElection,$this->niveau,$this->params,$listeAnnees,$listeCandidats,$this->tableCandidat);
		}
		else return FALSE;
	}
	
	public function getGridAnalyserSuivantLocalite(){
		if(!empty($_GET['listeLocalites']) AND !empty($_GET['listeCandidats'])){		
			$listeLocalites=explode(",",$_GET['listeLocalites']);
			$listeCandidats=explode(",",$_GET['listeCandidats']);
			$this->analysisModel->getGridAnalyserSuivantLocalite($this->typeElection,$this->niveau,$this->params,$listeLocalites,$listeCandidats,$this->tableCandidat);
		} else return false;	
	}
}

/* End of file analyser.php */
/* Location: ./application/controllers/analyser.php */