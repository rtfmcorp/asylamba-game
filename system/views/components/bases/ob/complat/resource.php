<?php
echo '<div class="component new-message">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Envoi de ressources</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-giveresource/baseid-' . $ob_compPlat->rPlace . '" method="post">';
				echo '<p><label for="send-resources-target">Base destinataire</label></p>';
				echo '<input class="autocomplete-hidden" type="hidden" name="otherbaseid" />';
				echo '<p class="input input-text"><input type="text" id="send-resources-target" class="autocomplete-orbitalbase" name="name" autocomplete="off" /></p>';

				echo '<p><label for="send-resources-quantity">Nombre de ressources</label></p>';
				echo '<p class="input input-text"><input type="text" id="send-resources-quantity" name="quantity" /></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>