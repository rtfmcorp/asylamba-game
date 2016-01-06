<?php
echo '<div class="component new-message">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('updatefactiondesc') . '" method="POST" />';
				echo '<h4>Editer la description</h4>';

				echo '<p class="input input-area"><textarea name="description" required style="height: 400px;">' . $faction->description . '</textarea></p>';
				echo '<p class="button"><button type="submit">Modifier</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Aperçu</h4>';
			echo '<p class="long-info text-bloc">' . $faction->getParsedDescription() . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>