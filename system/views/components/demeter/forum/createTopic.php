<?php
# createTopic component
# in demeter.forum package

# création d'un topic

# require

echo '<div class="component topic size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Création d\'un nouveau sujet</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="message write">';
				echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
				echo '<div class="content">';
					echo '<form action="' . APP_ROOT . 'action/a-createtopicforum/rforum-' . CTR::$get->get('forum') . '" method="POST">';
						echo '<input class="title" type="text" name="title" placeholder="sujet" />';
						echo '<textarea name="content" placeholder="message"></textarea>';
						echo '<input type="submit" value="créer le sujet" />';
					echo '</form>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';