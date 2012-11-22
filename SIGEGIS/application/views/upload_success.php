<html>
<head>
<title>Formulaire upload</title>
</head>
<body>

<h3>Le fichier a &eacute;t&eacute; correctement upload&eacute; !</h3>

<ul>
<?php foreach($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<p><?php echo anchor('admin/upload', 'Retour'); ?></p>

</body>
</html>