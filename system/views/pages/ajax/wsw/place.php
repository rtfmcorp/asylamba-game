<?php
echo '<div class="modal">';
	echo '<div class="header">';
		echo '<h2>Insérer une base</h2>';
		echo '<button class="wsw-box-cancel">X</button><br/>';
	echo '</div>';
	
	echo '<form action="#" method="POST">';
		echo '<input type="hidden" name="place-id" id="wsw-pl-id" class="autocomplete-hidden" />';
		echo '<input type="text" class="autocomplete-orbitalbase" autocomplete="off" />';
	echo '</form>';

	echo '<div class="footer">';
		echo '<button class="wsw-box-submit">Ok</button><br/>';
	echo '</div>';
echo '</div>';
?>