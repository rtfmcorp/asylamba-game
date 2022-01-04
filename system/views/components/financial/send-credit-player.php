<?php
# routeFinancial component
# in athena package

# détail les routes commerciales par base

# require
	# [{orbitalBase}]			ob_routeFinancial

# view part

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component new-message">';
	echo '<div class="head skin-2">';
		echo '<h2>Envoi de crédit</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>A un joueur</h4>';
			echo '<form action="' . Format::actionBuilder('sendcredit', $sessionToken) . '" method="post" />';
				echo '<p><label for="send-credit-target">Destinataire</label></p>';
				echo '<p class="input input-text">';
					echo '<input type="hidden" class="autocomplete-hidden" name="playerid" />';
					echo '<input type="text" id="send-credit-target" class="autocomplete-player" name="name" />';
				echo '</p>';

				echo '<p><label for="send-credit-credit">Nombre de crédit</label></p>';
				echo '<p class="input input-text"><input type="text" id="send-credit-credit" name="quantity" /></p>';

				echo '<p><label for="send-credit-message">Votre message (* facultatif)</label></p>';
				echo '<p class="input input-area"><textarea id="send-credit-message" name="text"></textarea></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
