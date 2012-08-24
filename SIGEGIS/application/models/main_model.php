<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Main_model extends CI_Model{
//private $table="presidentielle2007";
	private $table="presidentielle2012";
	
public function getHisto($balise){
		

		if(!empty($_GET["niveau"]))	{
			$niveau=$_GET["niveau"];
		}	else $niveau=null;
		
		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
								
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];		
		}
		else $parametres="1,2012,premier_tour,globaux";
		
		$params=explode(",",$parametres);
		$v=0;
		
		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource, SUM( nbVoix ) as nbVoix
		FROM resultatspresidentielles rp
		LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		
		
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		
		if ($niveau=="cen") $parametres3="centre.idCentre";
		elseif ($niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($niveau=="reg") $parametres3="region.idRegion";
		elseif ($niveau=="pays") $parametres3="pays.idPays";
		else $parametres3="null";
		
		$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
		
		for($i=0;$i<sizeof($params);$i++) {
			if($v++){
				if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
		
		$requete.=" GROUP BY idCandidature";
		
		$resultats=$this->db->query($requete)->result();
	
		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Résultats ";
		if ($niveau=="cen") {$titre_niveau.="par centre ";$sous_titre="Centre: ";}
		elseif ($niveau=="dep") {$titre_niveau.="départementaux ";$sous_titre="Département: ";}
		elseif($niveau=="reg") {$titre_niveau.="régionaux ";$sous_titre="Région: ";}
		elseif($niveau=="pays") {$titre_niveau.="par pays ";$sous_titre="Pays: ";}
		else  $titre_niveau.="globaux ";
		$titre_niveau.="de l'élection présidentielle de ".$resultats[0]->annee;

		
		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv1")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";

		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
		
		$i=0;$j=0;
		$abscisse="";$ordonnee="";
		
		foreach ($resultats as $resultat){
			if (!($i++)) $abscisse.="'$resultat->nomCandidat'";
			else $abscisse.=",'$resultat->nomCandidat'";
			if (!($j++)) $ordonnee.=$resultat->nbVoix;
			else $ordonnee.=",$resultat->nbVoix";
		}

		if(!empty($_GET['unite'])){if ($_GET['unite']=="va") $unite="En valeurs absolues"; else $unite="En valeurs relatives";} else  $unite="En valeurs absolues";
		
		// ----------------------------------------	//
		//					RENDU					//
		// ----------------------------------------	//
		
		return "<script type='text/javascript'>
		$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '$balise',
                type: 'column'
            },
            title: {
                text: \"$titre\"
            }, 
            subtitle: {
			    text: \"$sous_titre\"
			},          
            xAxis: {
                categories: [$abscisse],
                labels: {
                   rotation: -40,                   
                    align: 'right',
                    style: {
                    	width:20,
                        fontSize: '12px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
                //xAxis.labels.step:true 
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'NbVoix ($unite)'
                }
            },
            exporting: { 	
				enabled: false
			},
			navigation:{}, // ***************	navigation	**************** //
            legend: {
                layout: 'vertical',
                backgroundColor: '#FFFFFF',
                align: 'right',
                verticalAlign: 'top',
                //x: 80,
                //y: 70,
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return  this.x +': '+ this.y;
                }
            },
            
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    colorByPoint: true,
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            credits: {
    enabled: false
  },
            series: [{name:'Résultats',data:[$ordonnee]}]
        });
    });
    
});
		</script>";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       	
}
	
public function getPie($balise){		

		if(!empty($_GET["niveau"]))	{
			$niveau=$_GET["niveau"];
		}	else $niveau=null;
		
		if ($niveau=="cen") $nomLieu="nomCentre,";
		elseif ($niveau=="dep") $nomLieu="nomDepartement,";
		elseif ($niveau=="reg") $nomLieu="nomRegion,";
		elseif ($niveau=="pays") $nomLieu="nomPays,";
		else $nomLieu="";
								
		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];		
		}
		else $parametres="1,2012,premier_tour,globaux";
		
		$params=explode(",",$parametres);
		$v=0;
		
		$requete="SELECT rp.idCandidature, YEAR(dateElection) as annee, nomCandidat, $nomLieu nomSource, SUM( nbVoix ) as nbVoix
		FROM resultatspresidentielles rp
		LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
		LEFT JOIN source ON rp.idSource = source.idSource
		LEFT JOIN election ON rp.idElection = election.idElection
		LEFT JOIN centre ON rp.idCentre = centre.idCentre";
		
		
		
		if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
			LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
		if ($niveau=="reg" OR $niveau=="pays")
			$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
		if ($niveau=="pays")
			$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
		
		
		if ($niveau=="cen") $parametres3="centre.idCentre";
		elseif ($niveau=="dep") $parametres3="departement.idDepartement";
		elseif ($niveau=="reg") $parametres3="region.idRegion";
		elseif ($niveau=="pays") $parametres3="pays.idPays";
		else $parametres3="null";
		
		$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
		
		for($i=0;$i<sizeof($params);$i++) {
			if($v++){
				if ($colonnesBDD[$i]!="null") $requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
			}
			else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
		}
		
		$requete.=" GROUP BY idCandidature";
		
		$resultats=$this->db->query($requete)->result();
	
		
		// ----------------------------------------	//
		//			TITRES DES DIAGRAMMES			//
		// ----------------------------------------	//
		$titre_niveau="Résultats ";
		if ($niveau=="cen") {$titre_niveau.="par centre ";$sous_titre="Centre: ";}
		elseif ($niveau=="dep") {$titre_niveau.="départementaux ";$sous_titre="Département: ";}
		elseif($niveau=="reg") {$titre_niveau.="régionaux ";$sous_titre="Région: ";}
		elseif($niveau=="pays") {$titre_niveau.="par pays ";$sous_titre="Pays: ";}
		else  $titre_niveau.="globaux ";
		$titre_niveau.="de l'élection présidentielle de ".$resultats[0]->annee;

		
		if ($niveau=="cen") $sous_titre.=  $resultats[0]->nomCentre;
		elseif ($niveau=="dep") $sous_titre.=  $resultats[0]->nomDepartement;
		elseif ($niveau=="reg") $sous_titre.=  $resultats[0]->nomRegion;
		elseif ($niveau=="pays") $sous_titre.=  $resultats[0]->nomPays;
		else $sous_titre="";
		$titre=($balise=="chartdiv2")?$titre_niveau:"Erreur sur l'emplacement de l'histogramme !";

		// ----------------------------------------	//
		//			COLLECTE DES DONNEES			//
		// ----------------------------------------	//
						
		$line="";
		$i=0;
		
		foreach ($resultats as $resultat) 
			if (!($i++)) $line.="{name: '$resultat->nomCandidat',y: $resultat->nbVoix,sliced: true,selected: true}";
				else $line.=",['$resultat->nomCandidat',$resultat->nbVoix]";
				
		return "<script>$(function () {
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: '$balise',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: \"$titre\"
					},
					subtitle: {
					    text: \"$sous_titre\"
			        },
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
								}
							},showInLegend: true
						}
							
					},
					credits: {
    enabled: false
  },
					series: [{
						type: 'pie',
						name: 'Browser share',
						data: [$line]
					}]
				});
			});
		
		});</script>";
		
	}
	
	
	public function tableau(){
		
		$page = $_GET['page'];
		$limit = $_GET['rows'];
		$sidx = $_GET['sidx'];
		$sord = $_GET['sord'];
	
		if(!$sidx) $sidx =1;
				

		if(!empty($_GET['param'])){
			$parametres=$_GET['param'];
			$params=explode(",",$parametres);
			$v=0;			
			$parametres3=null;
			
			$requete="SELECT rp.idCandidature, nomCandidat,nomSource, SUM( nbVoix ) as nbVoix
			FROM resultatspresidentielles rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
			
			if(!empty($_GET["niveau"]))	{
				$niveau=$_GET["niveau"];
			}	else $niveau=null;
			
			if ($niveau=="dep" OR $niveau=="reg" OR $niveau=="pays")
				$requete.=" LEFT JOIN collectivite ON centre.idCollectivite = collectivite.idCollectivite
				LEFT JOIN departement ON collectivite.idDepartement = departement.idDepartement";
			if ($niveau=="reg" OR $niveau=="pays")
				$requete.=" LEFT JOIN region ON departement.idRegion = region.idRegion";
			if ($niveau=="pays")
				$requete.=" LEFT JOIN pays ON region.idPays = pays.idPays";
			
			if ($niveau=="cen") $parametres3="centre.idCentre";
			elseif ($niveau=="dep") $parametres3="departement.idDepartement";
			elseif ($niveau=="reg") $parametres3="region.idRegion";
			elseif ($niveau=="pays") $parametres3="pays.idPays";
			
		
			$colonnesBDD=array("rp.idSource","YEAR(election.dateElection)","election.tour",$parametres3);
			
			for($i=0;$i<sizeof($params);$i++) {
				if( $colonnesBDD[$i] ){
					if($v++)$requete.=" AND $colonnesBDD[$i]='".$params[$i]."'";
					else $requete.=" WHERE $colonnesBDD[$i]='".$params[$i]."'";
				}
			}
			
			$requeteCount="SELECT COUNT(DISTINCT S.idCandidature) as total FROM (SELECT rp.idCandidature, nomCandidat,nomSource
			FROM resultatspresidentielles rp
			LEFT JOIN candidature ON rp.idCandidature = candidature.idCandidature
			LEFT JOIN source ON rp.idSource = source.idSource
			LEFT JOIN election ON rp.idElection = election.idElection
			LEFT JOIN centre ON rp.idCentre = centre.idCentre";
			
			$requeteCount.=" GROUP BY rp.idCandidature) as S";
			
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
			
			$requete.=" GROUP BY idCandidature ORDER BY $sidx $sord LIMIT $start,$limit";
			
			$resultats=$this->db->query($requete)->result();
		}
		
			
		header("Content-type: text/xml;charset=utf-8");
	
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s .=  "<rows>";
		$s .= "<page>".$page."</page>";
		$s .= "<total>".$total_pages."</total>";
		$s .= "<records>".$totalRows."</records>";
	
		foreach ($resultats as $row) {
			$s .= "<row id='". $row->idCandidature ."'>";
			$s .= "<cell>". $row->nomCandidat ."</cell>";
			$s .= "<cell>". $row->nbVoix ."</cell>";			
			$s .= "</row>";
		}
		$s .= "</rows>";
	
		echo $s;	
	}
}