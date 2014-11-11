<?php
echo '<div class="component new-message params size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-writetofaction" method="POST" />';
				echo '<h4>Envoyer un message à la faction</h4>';

				echo '<p>Ciblage</p>';
				echo '<label class="checkbox">';
					echo '<input type="checkbox" name="target-n3" id="test" />';
					echo 'Envoyer au gouvernement';
				echo '</label>';

				echo '<label class="checkbox">';
					echo '<input type="checkbox" name="target-n2" value="2" />';
					echo 'Envoyer au sénat';
				echo '</label>';

				echo '<label class="checkbox">';
					echo '<input type="checkbox" name="target-n1" value="3" />';
					echo 'Envoyer au peuple';
				echo '</label>';

				echo '<p>Votre message</p>';
				echo '<p class="input input-area"><textarea id="new-message-message" name="message" required></textarea></p>';
				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>