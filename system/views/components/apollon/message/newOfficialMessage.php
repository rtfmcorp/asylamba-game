<?php
# newOfficialMessage componant
# in apollon package

# formulaire de création de message, autocomplétion des joueurs

# require
	# NULL

include_once ZEUS;

echo '<div class="component new-message size2">';
	echo '<div class="head">';
		echo '<h1>Messagerie</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-writeofficial" method="POST" />';
				echo '<p><label for="new-message-target">Destinataire</label></p>';
				echo '<p class="input input-text"><input type="text" id="new-message-target" name="player" placeholder="laissez-vide pour envoyer un message à tout les joueurs" /></p>';

				echo '<p><label for="new-message-faction">Faction (ne rien selectionner pour envoyer à toutes les factions)</label></p>';
				echo '<p class="input input-text">';
					echo '<select id="new-message-faction" name="ally">';
						echo '<option value="">Toutes les factions</option>';

						for ($i = 1; $i <= 7; $i++) { 
							echo '<option value="' . $i . '">' . ColorResource::getInfo($i, 'officialName') . '</option>';
						}
					echo '</select> ';
				echo '</p>';

				echo '<p><label for="new-message-message">Votre message</label></p>';
				echo '<p class="input input-area"><textarea id="new-message-message" name="message"></textarea></p>';

				echo '<p class="button"><input type="submit" value="envoyer" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>