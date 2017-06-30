<?php
# newOfficialMessage componant
# in apollon package

# formulaire de création de message, autocomplétion des joueurs

# require
	# NULL

use Asylamba\Classes\Library\Format;

echo '<div class="component new-message size2">';
	echo '<div class="head">';
		echo '<h1>Messagerie</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('writeofficialconversation', $this->getContainer()->get('app.session')->get('token')) . '" method="POST" />';
				echo '<p><label for="new-message-message">Votre message</label></p>';
				echo '<p class="input input-area"><textarea id="new-message-message" name="message"></textarea></p>';

				echo '<p class="button"><input type="submit" value="envoyer" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';