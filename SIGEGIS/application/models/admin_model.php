<?php if ( ! defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Description: Administration de la plateforme
 * @author Amadou SOW && Abdou Khadre GUEYE DESS | 2ITIC 2011-2012 
 */

class Admin_model extends CI_Model{
private $tables=array("presidentielle"=>"resultatspresidentielles","legislative"=>"resultatslegislatives","municipale"=>"resultatsmunicipales","regionale"=>"resultatsregionales","rurale"=>"resultatsrurales");
private $tablesParticipation=array("presidentielle"=>"participationpresidentielles","legislative"=>"participationlegislatives","municipale"=>"participationmunicipales","regionale"=>"participationregionales","rurale"=>"participationrurales");
private	$colors=array("#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300","#4572a7","#af5552","#89a057","#9982b4","#abc1e6","#5e8bc0","#bd9695","#ee9953","#ed66a3","#96b200","#b2b5b7","#b251b7","#4c1eb7","#ff6300");
private $titreElection="";
private $typeLocalite="";
private $candidatOrListe=array("candidat"=>"idCandidat","listescoalitionspartis"=>"idListe");
private $tableCandidat;

public function __construct(){
	if(!empty($_GET["typeElection"])) {
		$typeElection=$_GET["typeElection"];	
		if ($typeElection=="presidentielle") $this->tableCandidat="candidat";else $this->tableCandidat="listescoalitionspartis";
		if ($typeElection=="presidentielle") $this->titreElection="présidentielle";
		elseif ($typeElection=="legislative") $this->titreElection="législative";
		elseif ($typeElection=="regionale") $this->titreElection="régionale";
		else $this->titreElection=$typeElection;		
	}
}
		
	/**
	 * Cette fonction affiche le code xml du Grid 
	 * @return string
	 * @param string $balise Le nom du conteneur Html
	 */
	public function getGridResultats($typeElection,$niveau,$params){		
		
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		
		if(!$sidx) $sidx =1;

			$requete="SELECT * FROM {$this->tables[$typeElection]} rp  
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidat = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection";

			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($typeElection=="presidentielle") $colonnesBDD[]="election.tour";			
			if ($niveau=="dep") $colonnesBDD[]="rp.idDepartement";
			else $colonnesBDD[]="rp.idCentre";
			
			for($i=0;$i<sizeof($params);$i++) {
				if ($i) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";				
				else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
			}
			
			$requeteCount="SELECT COUNT(*) as total FROM {$this->tables[$typeElection]} rp  
			LEFT JOIN {$this->tableCandidat} ON rp.idCandidat = {$this->tableCandidat}.{$this->candidatOrListe[$this->tableCandidat]}
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
	$s .= "<cell>". $row->idElection ."</cell>";
	$s .= "<cell>". $row->idSource ."</cell>";
	$s .= "<cell>". $row->idCandidat ."</cell>";
	$s .= "<cell>". $row->idCentre ."</cell>";
	$s .= "<cell>". $row->idDepartement ."</cell>";
	$s .= "</row>";
	}
	$s .= "</rows>";
	
	echo $s;
	} 
	
	public function getGridParticipation($typeElection,$niveau,$params){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
	
			$v=0;
	
			$requete="SELECT * FROM {$this->tablesParticipation[$typeElection]} rp
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection";
			if ($niveau=="dep") $requete.=" LEFT JOIN departement ON rp.idDepartement = departement.idDepartement";
			else $requete.=" LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
			$colonnesBDD=array();
			$colonnesBDD[]="rp.idSource";
			$colonnesBDD[]="YEAR(election.dateElection)";
			if($typeElection=="presidentielle") $colonnesBDD[]="election.tour";
			if ($niveau=="dep") $colonnesBDD[]="rp.idDepartement";
			else $colonnesBDD[]="rp.idCentre";
	
			for($i=0;$i<sizeof($params);$i++) {
			if ($i) $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
	
		$requeteCount="SELECT COUNT(*) as total FROM {$this->tablesParticipation[$typeElection]} rp
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection";
		if ($niveau=="dep") $requeteCount.=" LEFT JOIN departement ON rp.idDepartement = departement.idDepartement";
		else $requeteCount.=" LEFT JOIN centre ON rp.idCentre = centre.idCentre";
	
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
	

	public function getGridElections($typeElection,$niveau,$params){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
	
		if(!$sidx) $sidx =1;
					
		$requete="SELECT * FROM election";
		if ($typeElection!="all") $requete.= " WHERE typeElection='$typeElection'";		
		
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
	
	public function getGridSources($typeElection,$niveau,$params){
	
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
	
	public function getGridUsers($typeElection,$niveau,$params){
	
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
	
	public function getGridCandidats($typeElection,$niveau,$params,$annee){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
			
		if(!$sidx) $sidx =1;
	
		if (!empty($annee)){
			if($annee=="all")
				$requete="SELECT * FROM candidat";
			else		
				$requete="SELECT idCandidat,prenom,nom,dateNaissance,lieuNaissance,partis,commentaires 
				FROM candidat WHERE idCandidat in (
				SELECT DISTINCT rp.idCandidat FROM {$this->tables[$typeElection]} rp 
				LEFT JOIN candidat ON rp.idCandidat = candidat.idCandidat
				LEFT JOIN election ON rp.idElection = election.idElection  
				WHERE YEAR(election.dateElection)=$annee)";
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
			$s .= "<row id='".$row->idCandidat."'>";
			$s .= "<cell>". $row->idCandidat ."</cell>";
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
	
	/**
	 * Fournit toutes les listes de partis et de coalitions 
	 */
	public function getGridCoalitionsPartis($typeElection,$niveau,$params,$annee){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];		
		
		if(!$sidx) $sidx =1;
		
		if (!empty($annee)){
			if($annee=="all")
				$requete="SELECT * FROM listesCoalitionsPartis";
			else
				$requete="SELECT idListe,nomListe,typeListe,partis,commentaires
				FROM listesCoalitionsPartis WHERE idListe in (
				SELECT DISTINCT rp.idCandidat as idListe 
				FROM {$this->tables[$typeElection]} rp
				LEFT JOIN listesCoalitionsPartis ON rp.idCandidat = listesCoalitionsPartis.idListe
				LEFT JOIN election ON rp.idElection = election.idElection 
				WHERE YEAR(election.dateElection)=$annee)";
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
			$s .= "<cell>". $row->commentaires ."</cell>";
			$s .= "<cell></cell>";
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;
	} 
	
	public function getGridLocalites($typeElection,$niveau,$params,$typeLocalite,$annee){
	
		$page = $_POST['page'];	$limit = $_POST['rows']; $sidx = $_POST['sidx']; $sord = $_POST['sord'];
		
		if(empty($annee)) return ;
	
		if(!$sidx) $sidx =1;
		
		$requete="SELECT * FROM $typeLocalite";
						
		if ($typeLocalite=="centre")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite";
		if ($typeLocalite=="collectivite" OR $typeLocalite=="centre")
			$requete.=" LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($typeLocalite=="departement" OR $typeLocalite=="collectivite" OR $typeLocalite=="centre")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($typeLocalite=="region" OR $typeLocalite=="departement" OR $typeLocalite=="collectivite" OR $typeLocalite=="centre")
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
			
			if ($typeLocalite=="centre"){
				$s .= "<row id='".$row->idCentre."'>";
				$s .= "<cell>". $row->idCentre ."</cell>";
				$s .= "<cell>". $row->nomCentre ."</cell>";
				$s .= "<cell>". $row->idCollectivite ."</cell>";
				$s .= "</row>";
			}
			
			if ($typeLocalite=="collectivite"){
				$s .= "<row id='".$row->idCollectivite."'>";
				$s .= "<cell>". $row->idCollectivite ."</cell>";
				$s .= "<cell>". $row->nomCollectivite ."</cell>";
				$s .= "<cell>". $row->idDepartement ."</cell>";
				$s .= "</row>";
			}
			
			if ($typeLocalite=="departement"){
				$s .= "<row id='".$row->idDepartement."'>";
				$s .= "<cell>". $row->idDepartement ."</cell>";
				$s .= "<cell>". $row->nomDepartement ."</cell>";
				$s .= "<cell>". $row->idRegion ."</cell>";
				$s .= "</row>";
			}
			
			if ($typeLocalite=="region"){
				$s .= "<row id='".$row->idRegion."'>";
				$s .= "<cell>". $row->idRegion ."</cell>";
				$s .= "<cell>". $row->nomRegion ."</cell>";
				$s .= "<cell>". $row->idPays ."</cell>";
				$s .= "</row>";
			}
			
			if ($typeLocalite=="pays"){
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
	
	public function resultatCRUD($typeElection,$session){
		
		$idResultat = $this->input->post('idResultat');
		$nbVoix = $this->input->post('nbVoix');
		$idElection = $this->input->post('idElection');
		$idSource = $this->input->post('idSource');
		$idCandidat = $this->input->post('idCandidat');
		$idCentre = $this->input->post('idCentre');
		$idDepartement = $this->input->post('idDepartement');
		
		$data = array(
				'nbVoix' => $nbVoix,
				'idElection' => $idElection,
				'idSource' => $idSource,
				'idCandidat' => $idCandidat,
				'idCentre' => $idCentre,
				'idDepartement' => $idDepartement
		);
		
		if($_POST['oper']=='add')
		{			
			$this->db->insert($this->tables[$typeElection], $data);		
		}
		if($_POST['oper']=='edit')
		{									
			$this->db->update($this->tables[$typeElection], $data, array('idResultat' => $idResultat));											
		}
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete($this->tables[$typeElection], array('idResultat' => $_POST['id']));
		}		
	}
	
	public function participationCRUD($typeElection,$session){
	
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
			$this->db->insert($this->tablesParticipation[$typeElection], $data);
		}
		if($_POST['oper']=='edit')
		{
			$this->db->update($this->tablesParticipation[$typeElection], $data, array('idParticipation' => $idParticipation));
		}
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete($this->tablesParticipation[$typeElection], array('idParticipation' => $_POST['id']));
		}
	}
	
	public function electionCRUD($typeElection,$session){
	
		$idElection = $this->input->post('idElection');
		$dateElection = $this->input->post('dateElection');
		$date = explode("/", $dateElection);
		$dateElection=date("Y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2]));		
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
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete("election", array('idElection' => $_POST['id']));
		}
	}
	
	public function sourceCRUD($typeElection,$session){
	
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
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete("source", array('idSource' => $_POST['id']));
		}
	}
	
	public function userCRUD($typeElection,$session){
	
		$id = $this->input->post('id');
		$username = $this->input->post('username');
		$oldPassword = $this->input->post('oldpassword');
		$newPassword = $this->input->post('newpassword');
		$level = $this->input->post('level');		
	
		$data = array(
				'username' => $username,				
				'level' => $level
		);				
	
		if($_POST['oper']=='add' AND $session['level'] == ADMIN)
		{
			$data['password']=$this->encrypt->sha1($newPassword);
			$this->db->insert("users", $data);
		}
		if($_POST['oper']=='edit' AND $session['level'] == ADMIN)
		{
			if(!empty($newPassword)) $data['password']=$this->encrypt->sha1($newPassword);
			else $data['password']=$oldPassword;
			$this->db->update("users", $data, array('id' => $id));			
		}
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete("users", array('id' => $_POST['id']));
		}
	}
	public function localiteCRUD($typeElection,$session){
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
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete($localite, array($niveau[$localite][0] => $_POST['id']));
		}
	}
	
	public function candidatCRUD($typeElection,$session){
		$idCandidat = $this->input->post('idCandidat');
		$prenom = $this->input->post('prenom');
		$nom = $this->input->post('nom');
		$dateNaissance = $this->input->post('dateNaissance');
		$date = explode("/", $dateNaissance);
		$dateNaissance=date("Y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2]));
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
			$this->db->insert("candidat", $data);
		}
		if($_POST['oper']=='edit')
		{			
			$this->db->update("candidat", $data, array('idCandidat' => $idCandidat));
		}
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete("candidat", array('idCandidat' => $_POST['id']));
		}
	}
	
	public function listeCRUD($typeElection,$session){
		$idListe = $this->input->post('idListe');
		$nomListe = $this->input->post('nomListe');
		$typeListe = $this->input->post('typeListe');
		$partis = $this->input->post('partis');
		$commentaires = $this->input->post('commentaires');		
		
		$data = array(
				'nomListe' => $nomListe,
				'typeListe' => $typeListe,
				'partis' => $partis,
				'commentaires' => $commentaires
		);
		
		if($_POST['oper']=='add')
		{
			$this->db->insert("listesCoalitionsPartis", $data);
		}		
		if($_POST['oper']=='edit')
		{	
			$this->db->update("listesCoalitionsPartis", $data, array('idListe' => $idListe));	
		}
		if($_POST['oper']=='del' AND $session['level'] == ADMIN)
		{
			$this->db->delete("listesCoalitionsPartis", array('idListe' => $_POST['id']));
		}
	}
	
	/**
	 * @todo Gérer les imporations CSV
	 * @param string $filename
	 * @param string $tablename
	 */
	public function importCSV($filename,$tablename){
		
		$requete="LOAD DATA INFILE '$filename' INTO TABLE $tablename";
		
		$this->db->query($requete);
	}
}