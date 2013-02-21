$(document).ready(function() {
	$("#bloc_visualiser,#bloc_analyser").hide();
	
	$(":input").attr("disabled","disabled");
	
	$(".boutonsimple").on("click",function(){
		$("#bloc_visualiser,#bloc_analyser").hide();
		$("#bloc_"+$(this).attr("id")).show("animated");
	});
});