<?php if ( ! defined('BASEPATH') ) exit('No direct script access allowed');
/**
 * 
 * @author Amadou SOW && Abdou Khadre GUEYE DESS | 2ITIC 2011-2012
 * Description: Administration de la plateforme 
 *
 */

class Admin_model extends CI_Model{
private $tables=array("presidentielle"=>"resultatspresidentielles2","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
private $tablesParticipation=array("presidentielle"=>"participationpresidentielles2","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
private $typeElection=null;
private $titreElection="";
private $typeLocalite="";
private $la_session;
private $niveau=null;
private $candidatOrListe=array("candidature"=>"idCandidature","listescoalitionspartis"=>"idListe");
private $tableCandidat;

public function __construct(){
	if(!empty($_GET["typeElection"])) {
		$this->typeElection=$_GET["typeElection"];	
		if ($this->typeElection=="presidentielle") $this->tableCandidat="candidature";else $this->tableCandidat="listescoalitionspartis";
		if ($this->typeElection=="presidentielle") $this->titreElection="présidentielle";
		elseif ($this->typeElection=="legislative") $this->titreElection="législative";
		elseif ($this->typeElection=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$this->typeElection;		
	}
		
	if(!empty($_GET['niveau'])) $this->niveau=$_GET['niveau'];
	
	if(!empty($_GET["typeLocalite"])) 	$this->typeLocalite=$_GET["typeLocalite"];		
	
	$this->la_session=$this->session->userdata('logged_in');
}
		
	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridVisualiser(){		
		
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		
		if(!$sidx) $sidx =1;
		
		if(!empty($_GET['param']))
		{
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
		
			$requete="SELECT * FROM {$this->tables[$this->typeElection]} rp  
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection";

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";			
			if ($this->niveau=="dep") $colonnesBDD[]="rp.idDepartement";
			else $colonnesBDD[]="rp.idCentre";
			
			for($i=0;$i<sizeof($params);$i++) {
				if ($i) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";				
				else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
			
			$requeteCount="SELECT COUNT(*) as total FROM {$this->tables[$this->typeElection]} rp  
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidature = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection";
			
			for($i=0;$i<sizeof($params);$i++) {
				if ($i) $requeteCount.=" AND $colonnesBDD[$i]='".$params[$i]."'";				
				else $requeteCount.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
			 				
			$count = $this->db->query($requeteCount)->result();
		
		    $totalRows=$count[0]->total;
		
			if( $totalRows > 0 && $limit > 0) {
				$total_pages = ceil($totalRows/$limit);
			}
			else $total_pages = 0;			
		
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
	} 
	
	public function getGridParticipation(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
	
		if(!empty($_GET['param']))
		{
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;
	
			$requete="SELECT * FROM {$this->tablesParticipation[$this->typeElection]} rp
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection";
	
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($this->typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			if ($this->niveau=="dep") $colonnesBDD[]="rp.idDepartement";
	
			for($i=0;$i<sizeof($params);$i++) {
			if ($i) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
	
		$requeteCount="SELECT COUNT(*) as total FROM {$this->tablesParticipation[$this->typeElection]} rp
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection";
	
		for($i=0;$i<sizeof($params);$i++) {
		if ($i) $requeteCount.=" AND $colonnesBDD[$i]='".$params[$i]."'";
		else $requeteCount.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
	
		$count = $this->db->query($requeteCount)->result();
	
		$totalRows=$count[0]->total;
	
		if( $totalRows > 0 && $limit > 0) {
		$total_pages = ceil($totalRows/$limit);
	}
	else $total_pages = 0;
	
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
	$s .= "<row id='".$row->idParticipation."'>";
	$s .= "<cell>". $row->idParticipation ."</cell>";
	$s .= "<cell>". $row->nbInscrits ."</cell>";
	$s .= "<cell>". $row->nbVotants ."</cell>";
	$s .= "<cell>". $row->nbBulletinsNuls ."</cell>";
	$s .= "<cell>". $row->nbExprimes ."</cell>";
	$s .= "<cell>". $row->idElection ."</cell>";
	$s .= "<cell>". $row->idSource ."</cell>";
	$s .= "<cell>". $row->idCentre ."</cell>";
	$s .= "<cell>". $row->idDepartement ."</cell>";
	$s .= "</row>";
	}
	$s .= "</rows>";
	
	echo $s;
	} 
	

	public function getGridElections(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
					
		$requete="SELECT * FROM election";
		if ($this->typeElection!="all") $requete.= " WHERE typeElection='$this->typeElection'";		
		
		$totalRows=$this->db->query($requete)->num_rows();
		
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
	if ($page > $total_pages) $page=$total_pages;
	
	$start = $limit*$page - $limit;
	
	if($start <0) $start = 0;
	
	$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";

	$resultats=$this->db->query($requete)->result();
	
	header("Content-type: text/xml;charset=utf-8");
	
	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s .= "<rows>";
	$s .= "<page>".$page."</page>";
	$s .= "<total>".$total_pages."</total>";
	$s .= "<records>".$totalRows."</records>";
	
	foreach ($resultats as $row) {
	$s .= "<row id='".$row->idElection."'>";
	$s .= "<cell>". $row->idElection ."</cell>";
	$s .= "<cell>". $row->dateElection ."</cell>";
	$s .= "<cell>". $row->typeElection ."</cell>";
	$s .= "<cell>". $row->tour ."</cell>";
	$s .= "<cell>". $row->anneeDecoupage ."</cell>";	
	$s .= "</row>";
	}
	$s .= "</rows>";
	
	echo $s;
	}
	
	public function getGridSources(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
	
		$requete="SELECT * FROM source";
	
		$totalRows=$this->db->query($requete)->num_rows();
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;
	
		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
		$resultats=$this->db->query($requete)->result();
	
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
			$s .= "<row id='".$row->idSource."'>";
			$s .= "<cell>". $row->idSource ."</cell>";
			$s .= "<cell>". $row->nomSource ."</cell>";			
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
	} 
	
	public function getGridUsers(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
	
		$requete="SELECT * FROM users";
	
		$totalRows=$this->db->query($requete)->num_rows();
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;
	
		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
		$resultats=$this->db->query($requete)->result();
	
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
			$s .= "<row id='".$row->id."'>";
			$s .= "<cell>". $row->id ."</cell>";
			$s .= "<cell>". $row->username ."</cell>";
			$s .= "<cell>". $row->password ."</cell>";
			$s .= "<cell></cell>";
			$s .= "<cell>". $row->level ."</cell>";			
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
	}
	
	public function getGridCandidats(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		$annee= $_GET['annee'];
	
		if(!$sidx) $sidx =1;
	
		if (!empty($annee)){
			if($annee=="all")
				$requete="SELECT * FROM candidature";
			else
				$requete="SELECT DISTINCT candidature.* FROM {$this->tables[$this->typeElection]} rp 
				LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
				LEFT JOIN election ON rp.idElection = election.idElection 
				LEFT JOIN centre ON rp.idCentre = centre.idCentre 
				WHERE YEAR(election.dateElection)=$annee";
		}
		else return;
			
		$totalRows=$this->db->query($requete)->num_rows();
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;
	
		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
		$resultats=$this->db->query($requete)->result();
	
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
			$s .= "<row id='".$row->idCandidature."'>";
			$s .= "<cell>". $row->idCandidature ."</cell>";
			$s .= "<cell></cell>";
			$s .= "<cell>". $row->prenom ."</cell>";
			$s .= "<cell>". $row->nom ."</cell>";
			$s .= "<cell>". $row->dateNaissance ."</cell>";
			$s .= "<cell>". $row->lieuNaissance ."</cell>";
			$s .= "<cell>". $row->partis."</cell>";
			$s .= "<cell>". htmlspecialchars($row->commentaires) ."</cell>";
			$s .= "<cell></cell>";
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
	}
	
	public function getGridCoalitionsPartis(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		$annee= mysql_real_escape_string($_GET['annee']);
		
		if(!$sidx) $sidx =1;
		
		if (!empty($annee)){
			if($annee=="all")
				$requete="SELECT * FROM listesCoalitionsPartis";
			else
				$requete="SELECT DISTINCT listesCoalitionsPartis.* FROM {$this->tables[$this->typeElection]} rp
				LEFT JOIN listesCoalitionsPartis ON rp.idCandidature = listesCoalitionsPartis.idListe
				LEFT JOIN election ON rp.idElection = election.idElection
				LEFT JOIN centre ON rp.idCentre = centre.idCentre
				WHERE YEAR(election.dateElection)=$annee";
		}
		else return;
	
		$totalRows=$this->db->query($requete)->num_rows();
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;
	
		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
		$resultats=$this->db->query($requete)->result();
	
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
			$s .= "<row id='".$row->idListe."'>";
			$s .= "<cell>". $row->idListe ."</cell>";
			$s .= "<cell>". $row->nomListe ."</cell>";
			$s .= "<cell>". $row->typeListe ."</cell>";
			$s .= "<cell>". $row->partis ."</cell>";
			$s .= "<cell>". $row->infosComplementaires ."</cell>";
			$s .= "<cell></cell>";
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
	} 
	
	public function getGridLocalites(){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		$annee = $_GET['annee'];
		
		if(empty($annee)) return ;
	
		if(!$sidx) $sidx =1;
		
		$requete="SELECT * FROM $this->typeLocalite";
						
		if ($this->typeLocalite=="centre")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite";
		if ($this->typeLocalite=="collectivite" OR $this->typeLocalite=="centre")
			$requete.=" LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($this->typeLocalite=="departement" OR $this->typeLocalite=="collectivite" OR $this->typeLocalite=="centre")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($this->typeLocalite=="region" OR $this->typeLocalite=="departement" OR $this->typeLocalite=="collectivite" OR $this->typeLocalite=="centre")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		$requete.= " WHERE pays.anneeDecoupage=$annee"; 
		
		$totalRows=$this->db->query($requete)->num_rows();
	
		if( $totalRows > 0 && $limit > 0) $total_pages = ceil($totalRows/$limit);
		else $total_pages = 0;
	
		if ($page > $total_pages) $page=$total_pages;
	
		$start = $limit*$page - $limit;
	
		if($start <0) $start = 0;
	
		$requete.=" ORDER BY $sidx $sord LIMIT $start,$limit";
	
		$resultats=$this->db->query($requete)->result();
	
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .= "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";

		foreach ($resultats as $row) {
			
			if ($this->typeLocalite=="centre"){
				$s .= "<row id='".$row->idCentre."'>";
				$s .= "<cell>". $row->idCentre ."</cell>";
				$s .= "<cell>". $row->nomCentre ."</cell>";
				$s .= "<cell>". $row->idCollectivite ."</cell>";
				$s .= "</row>";
			}
			
			if ($this->typeLocalite=="collectivite"){
				$s .= "<row id='".$row->idCollectivite."'>";
				$s .= "<cell>". $row->idCollectivite ."</cell>";
				$s .= "<cell>". $row->nomCollectivite ."</cell>";
				$s .= "<cell>". $row->idDepartement ."</cell>";
				$s .= "</row>";
			}
			
			if ($this->typeLocalite=="departement"){
				$s .= "<row id='".$row->idDepartement."'>";
				$s .= "<cell>". $row->idDepartement ."</cell>";
				$s .= "<cell>". $row->nomDepartement ."</cell>";
				$s .= "<cell>". $row->idRegion ."</cell>";
				$s .= "</row>";
			}
			
			if ($this->typeLocalite=="region"){
				$s .= "<row id='".$row->idRegion."'>";
				$s .= "<cell>". $row->idRegion ."</cell>";
				$s .= "<cell>". $row->nomRegion ."</cell>";
				$s .= "<cell>". $row->idPays ."</cell>";
				$s .= "</row>";
			}
			
			if ($this->typeLocalite=="pays"){
				$s .= "<row id='".$row->idPays."'>";
				$s .= "<cell>". $row->idPays ."</cell>";
				$s .= "<cell>". $row->nomPays ."</cell>";
				$s .= "<cell>". $row->anneeDecoupage ."</cell>";
				$s .= "</row>";
			}
		}
		$s .= "</rows>";
	
		echo $s;
	}
	
	public function resultatCRUD(){
		
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
		
		if($_POST['oper']=='add')
		{			
			$this->db->insert($this->tables[$this->typeElection], $data);		
		}
		if($_POST['oper']=='edit')
		{									
			$this->db->update($this->tables[$this->typeElection], $data, array('idResultat' => $idResultat));											
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete($this->tables[$this->typeElection], array('idResultat' => $_POST['id']));
		}		
	}
	
	public function participationCRUD(){
	
		$idParticipation = $this->input->post('idParticipation');
		$nbInscrits = $this->input->post('nbInscrits');
		$nbVotants = $this->input->post('nbVotants');
		$nbBulletinsNuls = $this->input->post('nbBulletinsNuls');
		$nbExprimes = $this->input->post('nbExprimes');
		$idElection = $this->input->post('idElection');
		$idSource = $this->input->post('idSource');
		$idCentre = $this->input->post('idCentre');
		$idDepartement = $this->input->post('idDepartement');
	
		$data = array(
				'nbInscrits' => $nbInscrits,
				'nbVotants' => $nbVotants,
				'nbBulletinsNuls' => $nbBulletinsNuls,
				'nbExprimes' => $nbExprimes,
				'idElection' => $idElection,
				'idSource' => $idSource,
				'idCentre' => $idCentre,
				'idDepartement' => $idDepartement
		);
	
		if($_POST['oper']=='add')
		{
			$this->db->insert($this->tablesParticipation[$this->typeElection], $data);
		}
		if($_POST['oper']=='edit')
		{
			$this->db->update($this->tablesParticipation[$this->typeElection], $data, array('idParticipation' => $idParticipation));
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete($this->tablesParticipation[$this->typeElection], array('idParticipation' => $_POST['id']));
		}
	}
	
	public function electionCRUD(){
	
		$idElection = $this->input->post('idElection');
		$dateElection = $this->input->post('dateElection');
		$typeElection = $this->input->post('typeElection');
		$tour = $this->input->post('tour');
		$anneeDecoupage = $this->input->post('anneeDecoupage');
		
		$data = array(
				'dateElection' => $dateElection,
				'typeElection' => $typeElection,
				'tour' => $tour,
				'anneeDecoupage' => $anneeDecoupage
		);
		
		if($_POST['oper']=='add')
		{						
			$this->db->insert("election", $data);
		}
		if($_POST['oper']=='edit')
		{			
			$this->db->update("election", $data, array('idElection' => $idElection));	
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete("election", array('idElection' => $_POST['id']));
		}
	}
	
	public function sourceCRUD(){
	
		$idSource = $this->input->post('idSource');
		$nomSource = $this->input->post('nomSource');		
	
		$data = array(
			'nomSource' => $nomSource
		);
	
		if($_POST['oper']=='add')
		{
			$this->db->insert("source", $data);
		}
		if($_POST['oper']=='edit')
		{
			$this->db->update("source", $data, array('idSource' => $idSource));
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete("source", array('idSource' => $_POST['id']));
		}
	}
	
	public function userCRUD(){
	
		$id = $this->input->post('id');
		$username = $this->input->post('username');
		$oldPassword = $this->input->post('oldpassword');
		$newPassword = $this->input->post('newpassword');
		$level = $this->input->post('level');		
	
		$data = array(
				'username' => $username,				
				'level' => $level
		);				
	
		if($_POST['oper']=='add')
		{
			$data['password']=$this->encrypt->sha1($newPassword);
			$this->db->insert("users", $data);
		}
		if($_POST['oper']=='edit')
		{
			if(!empty($newPassword)) $data['password']=$this->encrypt->sha1($newPassword);
			else $data['password']=$oldPassword;
			$this->db->update("users", $data, array('id' => $id));			
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete("users", array('id' => $_POST['id']));
		}
	}
	public function localiteCRUD(){
		$localite=null;
		if (!empty($_GET["typeLocalite"])) $localite=$_GET["typeLocalite"];
	
		$niveau["pays"]=array("idPays","nomPays","anneeDecoupage");
		$niveau["region"]=array("idRegion","nomRegion","idPays");
		$niveau["departement"]=array("idDepartement","nomDepartement","idRegion");
		$niveau["collectivite"]=array("idCollectivite","nomCollectivite","idDepartement");
		$niveau["centre"]=array("idCentre","nomCentre","idCollectivite");
		
		$id = $this->input->post($niveau[$localite][0]);
		$nom = $this->input->post($niveau[$localite][1]);
		$parent = $this->input->post($niveau[$localite][2]);
		
		$data = array(
			$niveau[$localite][0] => $id,
			$niveau[$localite][1] => $nom,
			$niveau[$localite][2] => $parent
		);
		
		if($_POST['oper']=='add')
		{
			$this->db->insert($localite, $data);
		}
		if($_POST['oper']=='edit')
		{
			$this->db->update($localite, $data, array($niveau[$localite][0] => $id));
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete($localite, array($niveau[$localite][0] => $_POST['id']));
		}
	}
	
	public function candidatCRUD(){
		$idCandidature = $this->input->post('idCandidature');
		$prenom = $this->input->post('prenom');
		$nom = $this->input->post('nom');
		$dateNaissance = $this->input->post('dateNaissance');
		$lieuNaissance = $this->input->post('lieuNaissance');
		$partis = $this->input->post('partis');
		$commentaires = $this->input->post('commentaires');
		
		$data = array(
				'prenom' => $prenom,
				'nom' => $nom,
				'dateNaissance' => $dateNaissance,
				'lieuNaissance' => $lieuNaissance,
				'dateNaissance' => $dateNaissance,
				'partis' => $partis,
				'commentaires' => $commentaires
		);
		
		if($_POST['oper']=='add')
		{
			$this->db->insert("candidature", $data);
		}
		if($_POST['oper']=='edit')
		{			
			$this->db->update("candidature", $data, array('idCandidature' => $idCandidature));
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete("candidature", array('idCandidature' => $_POST['id']));
		}
	}
	
	public function listeCRUD(){
		$idListe = $this->input->post('idListe');
		$nomListe = $this->input->post('nomListe');
		$typeListe = $this->input->post('typeListe');
		$partis = $this->input->post('partis');
		$infosComplementaires = $this->input->post('infosComplementaires');		
		
		$data = array(
				'nomListe' => $nomListe,
				'typeListe' => $typeListe,
				'partis' => $partis,
				'infosComplementaires' => $infosComplementaires
		);
		
		if($_POST['oper']=='add')
		{
			$this->db->insert("listesCoalitionsPartis", $data);
		}		
		if($_POST['oper']=='edit')
		{	
			$this->db->update("listesCoalitionsPartis", $data, array('idListe' => $idListe));	
		}
		if($_POST['oper']=='del' AND $this->la_session['level']==1)
		{
			$this->db->delete("listesCoalitionsPartis", array('idListe' => $_POST['id']));
		}
	}
}