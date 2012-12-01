<title><?php echo $title;?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="print" href="<?php echo css_url("print"); ?>" />
<link href="<?php echo css_url("jquery-ui-1.9.2.custom/css/custom-theme-sigegis/jquery-ui-1.9.2.custom");?>" rel="stylesheet">	
<link href="<?php echo css_url("jquery-ui-1.9.2.custom/chosen/chosen");?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("ui.jqgrid"); ?>" />

<?php foreach ($styles as $style){?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url("$style"); ?>" />
<?php }?>

<script src="<?php echo js_url("jqgrid/js/jquery-1.7.2.min");?>" type="text/javascript"></script>

<!-- ------------------------------------------------------------------------------------ -->
<!--	 			Le script jquery-1.8.3 est incompatible avec jqGrid 				  -->
<!-- script src="<?php echo js_url("jquery-ui-1.9.2.custom/js/jquery-1.8.3");?>"></script -->
<!-- ------------------------------------------------------------------------------------ -->

<script src="<?php echo js_url("jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom");?>"></script>
<script src="<?php echo js_url("jquery-ui-1.9.2.custom/chosen/chosen.jquery.min")?>"></script>

<script src="<?php echo js_url("jqgrid/plugins/jquery.searchFilter");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/i18n/grid.locale-fr");?>" type="text/javascript"></script>
<script src="<?php echo js_url("jqgrid/js/jquery.jqGrid.min");?>" type="text/javascript"></script>
<script src="<?php echo js_url("highcharts/js/highcharts");?>"></script>
<script src="<?php echo js_url("highcharts/js/modules/exporting");?>"></script>

<meta name="Description" content="Max Design - standards based web design, development and training" />
<meta name="robots" content="all, index, follow" />
<link href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz" rel="stylesheet" type="text/css" />

<script type="text/javascript">	
$(function(){
	//Activation des tooltips
	 $( document ).tooltip();
});
</script>  