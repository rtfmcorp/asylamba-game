<?php
# newMessage componant
# in hermes package

# formulaire de création de message, autocomplétion des joueurs

# require
	# NULL

echo '<div class="component new-message">';
	echo '<div class="head">';
		echo '<h1>Messagerie</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-writemessage" method="POST" />';
				echo '<p><label for="new-message-target">Destinataire</label></p>';
				echo '<p class="input input-text">';
					echo '<input type="hidden" class="autocomplete-hidden" name="playerid" />';
					echo '<input type="text" id="new-message-target" class="autocomplete-player" name="name" />';
				echo '</p>';

				echo '<p><label for="new-message-message">Votre message</label></p>';
				echo '<p class="input input-area"><textarea id="new-message-message" name="message"></textarea></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>