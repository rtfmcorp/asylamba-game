<?php
# newMessage componant
# in hermes package

# formulaire de création de message, autocomplétion des joueurs

# require
	# NULL

echo '<div class="component topic size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Démarrer une nouvelle conversation</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="message write">';
				echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
				echo '<div class="content">';
					echo '<form action="' . APP_ROOT . 'action/a-writemessage" method="POST">';
						echo '<input type="hidden" class="autocomplete-hidden" name="playerid" />';
						echo '<input type="text" class="title autocomplete-player" name="name" placeholder="Destinataire" />';
						echo '<textarea name="message" placeholder="Votre message"></textarea>';
						echo '<button>Démarrer la conversation</button>';
					echo '</form>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>