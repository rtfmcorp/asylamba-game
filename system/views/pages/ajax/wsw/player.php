<?php
echo '<div class="modal">';
	echo '<div class="header">';
		echo '<h2>Insérer un joueur</h2>';
		echo '<button class="wsw-box-cancel">X</button><br/>';
	echo '</div>';
	
	echo '<form action="#" method="POST">';
		echo '<input type="text" class="autocomplete-player" autocomplete="off" id="wsw-py-pseudo" />';
	echo '</form>';

	echo '<div class="footer">';
		echo '<button class="wsw-box-submit">Ok</button><br/>';
	echo '</div>';
echo '</div>';
?>