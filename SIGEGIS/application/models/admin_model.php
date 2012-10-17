<?php if ( ! defined('BASEPATH') ) exit('No direct script access allowed');
/**
 * 
 * @author Amadou SOW && Abdou Khadre GUEYE DESS | 2ITIC 2011-2012
 * Description: Ce modèle gère l'exportation des données ainsi que leur affichage pour la partie dédié à la visualisation des données 
 *
 */

class Admin_model extends CI_Model{
private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
private $typeElection=null;
private $titreElection="";

public function __construct(){
	if(!empty($_GET["typeElection"])) {
		$this->typeElection=$_GET["typeElection"];	
		if ($this->typeElection=="presidentielle") $this->titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $this->titreElection="législative";
		elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$this->typeElection;
	}	
}
		
	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridVisualiser(){		
		
		$page = $_POST['page'];
		$limit = $_POST['rows'];
		$sidx = $_POST['sidx'];
		$sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
		
		if(!empty($_GET['param']))
		{
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
			
			$requete="SELECT * FROM {$this->tables[$this->typeElection]} rp  
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";						

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";			
			$colonnesBDD[]="centre.idCentre";
			
			for($i=0;$i<sizeof($params);$i++) {
				if ($i) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";				
				else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
			
			$requeteCount="SELECT COUNT(*) as total FROM {$this->tables[$this->typeElection]} rp  
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
			
			for($i=0;$i<sizeof($params);$i++) {
				if ($i) $requeteCount.=" AND $colonnesBDD[$i]='".$params[$i]."'";				
				else $requeteCount.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
			 				
			$count = $this->db->query($requeteCount)->result();
		
		    $totalRows=$count[0]->total;
		
			if( $totalRows > 0 && $limit > 0) {
				$total_pages = ceil($totalRows/$limit);
			}
			else {
				$total_pages = 0;
			}
		
			if ($page > $total_pages) $page=$total_pages;
		
			$start = $limit*$page - $limit;

			if($start <0) $start = 0;
		
			$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
		
			$resultats=$this->db->query($requete)->result();
		}

	
	header("Content-type: text/xml;charset=utf-8");

	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s .= "<rows>";
	$s .= "<page>".$page."</page>";
	$s .= "<total>".$total_pages."</total>";
	$s .= "<records>".$totalRows."</records>";
	
	foreach ($resultats as $row) {
	$s .= "<row id='".$row->idResultat."'>";
	$s .= "<cell>". $row->idResultat ."</cell>";
	$s .= "<cell>". $row->nbVoix ."</cell>";
	$s .= "<cell>". $row->valide ."</cell>";
	$s .= "<cell>". $row->idElection ."</cell>";
	$s .= "<cell>". $row->idSource ."</cell>";
	$s .= "<cell>". $row->idCandidature ."</cell>";
	$s .= "<cell>". $row->idCentre ."</cell>";
	$s .= "<cell>". $row->idDepartement ."</cell>";
	$s .= "</row>";
	}
	$s .= "</rows>";
	
	echo $s;
	} // ...............  Fin de getGrid() ...............

	public function editRP(){
		
		if($_POST['oper']=='add')
		{
		
		}
		if($_POST['oper']=='edit')
		{
			
			$idResultat = $this->input->post('idResultat');
			$nbVoix = $this->input->post('nbVoix');
			$valide = $this->input->post('valide');
			$idElection = $this->input->post('idElection');
			$idSource = $this->input->post('idSource');
			$idCandidature = $this->input->post('idCandidature');
			$idCentre = $this->input->post('idCentre');
			$idDepartement = $this->input->post('idDepartement');
				
			$data = array(
					'nbVoix' => $nbVoix,
					'valide' => $valide,
					'idElection' => $idElection,
					'idSource' => $idSource,
					'idCandidature' => $idCandidature,
					'idCentre' => $idCentre,
					'idDepartement' => $idDepartement
              );
 			
			$this->db->update($this->tables[$this->typeElection], $data, array('idResultat' => $idResultat));			
								
		}
		if($_POST['oper']=='del')
		{
		
		}		
	}
	
	}