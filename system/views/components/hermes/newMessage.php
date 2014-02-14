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
		$date1 = '2014-02-13 23:00:00';
		$date2 = '2014-02-14 01:00:00';
		echo 'date1 : ' . $date1 . '<br/>';
		echo 'date2 : ' . $date2 . '<br/>';
		Bug::pre(Utils::intervalDates($date1, $date2, 'd'));
			echo '<form action="' . APP_ROOT . 'action/a-writemessage" method="POST" />';
				echo '<p><label for="new-message-target">Destinataire</label></p>';
				echo '<p class="input input-text"><input type="text" id="new-message-target" class="autocomplete-player" name="name" /></p>';

				echo '<p><label for="new-message-message">Votre message</label></p>';
				echo '<p class="input input-area"><textarea id="new-message-message" name="message"></textarea></p>';

				echo '<p class="button"><input type="submit" value="envoyer" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>