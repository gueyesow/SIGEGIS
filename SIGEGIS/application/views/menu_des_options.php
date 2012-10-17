<div class="zone_des_options">
	<form action="">
		<fieldset id="types_elections">
			<legend>Type d'élection à représenter</legend>
			<input id="presidentielle" type="radio" name="radio" checked="checked" />
			<label for="presidentielle">Election présidentielle</label><br /> 
			<input id="legislative" type="radio" name="radio" />
			<label for="legislative">Election législative</label><br /> 
			<input id="locale" type="radio" name="radio" />
			<label for="locale">Election locale</label><br />
		</fieldset>

		<fieldset id="types_affichage">
			<legend>Mode de représentation</legend>
			<input type="checkbox" id="map" name="map" />
			<label for="map">Carte</label><br />
			<input type="checkbox" id="bar" checked="checked" name="bar" />
			<label for="bar">Diagramme en colonnes</label><br /> 
			<input type="checkbox" id="pie" name="pie" />
			<label for="pie">Diagramme à secteurs</label><br /> 							
			<input type="checkbox" id="line" name="line" />
			<label for="line">Courbes</label><br />
			<input type="checkbox" id="grid" name="grid" />
			<label for="grid">Tableau</label>
		</fieldset>

		<!--fieldset>
			<legend>Format des données</legend>
			<input id="valeur_absolue" type="radio" name="format"
				checked="checked" /><label for="valeur_absolue">Valeurs absolues</label><br />
			<input id="valeur_relative" type="radio" name="format" /><label
				for="valeur_relative">Valeurs relatives</label><br />
		</fieldset-->
	</form>
</div> <br> <br>