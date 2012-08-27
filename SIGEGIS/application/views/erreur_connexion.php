<form method="post" action="">
	<label for="login">Pseudo : </label> <input type="text" name="login"
		value="<?php echo
set_value('"login"'); ?>" />
	<?php echo form_error('"login"'); ?>
	<label for="password">Mot de passe :</label> <input type="password"
		name="password" value="" />
	<?php echo form_error('password'); ?>
	<input type="submit" value="Envoyer" />
</form>
