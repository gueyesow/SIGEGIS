<?php 
foreach ($scripts_array as $js){
?>
<script src="<?php echo js_php_url($js);?>" type="text/javascript"></script>
<?php 
}
?>

<script type="text/javascript">

/*Animation du panneau latéral */
var jumper = 1;

$("#openclose").click(function  () {
	
	if(jumper == 1){
		$("#pannelside").animate(
			{left:"-258px", opacity:0.75}, 500, 'easeOutBounce'
			);
		$("#openclose img ").attr("src","<?php echo img_url("open.png");?>"	);
	jumper = 0;
}
else {
	$("#pannelside").animate(
	{left:"0px", opacity:1}, 500, 'easeOutBounce'
	);
	$("#openclose img ").attr("src","<?php echo img_url("close.png");?>"	);
		jumper = 1;
	}
	
});

//Activation du pluggin Choosen
$(function(){

	// Activation du plugin une fois que toutes les données sont chargées dans les selectbox 
	$("#bloc_horizontal_filtres select:last").one("change", function() {
		$(".chzn-select").chosen({no_results_text: "Aucun résultat sur la liste!!!"});
	});
	
	// Raffraîchissement des selectbox du plugin (elles ne se mettent pas a jour toutes seules !!!)
	$("#bloc_horizontal_filtres select:last").on("change",function  () {		
		$("select").each(function(){
			$(this).trigger("liszt:updated"); // Update des listes deroulantes choosen 
			$("#bloc_horizontal_filtres span").css({"width":"100%"}); // le redimensionnement du conteneur de choosen ne se fait pas automatiquement  
		});		
	});

	/*
	$("h3").on("click", function() {
		$(".filtres_accordions2 .chzn-select").chosen({no_results_text: "Aucun résultat sur la liste!!!"});
		$(".filtres_accordions2 span").css({"width":"100%"}); // le redimensionnement du conteneur de choosen ne se fait pas automatiquement
	});

	$("#ouvrir").one("click",function(){
		$(".filtres_accordions1 .chzn-select").chosen({no_results_text: "Aucun résultat sur la liste!!!"});	
	});
	
	$(".filtres_accordions1 select:last").on("change",function  () {		
			$(this).trigger("liszt:updated"); 
			$("#localite_chzn").css({"width":"100%"});		
	});
	*/

	
});

</script>