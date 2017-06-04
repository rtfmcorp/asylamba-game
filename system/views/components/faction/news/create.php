<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get('app.session')->get('token');

echo '<div class="component new-message">';
	echo '<div class="head skin-2">';
		echo '<h2>Créer une annonce</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('writenews', $sessionToken) . '" method="POST" />';
				echo '<p>Titre de l\'annonce</p>';
				echo '<p class="input input-text"><input name="title" required /></p>';

				echo '<p>Contenu de l\'annonce</p>';
				echo '<p class="input input-area"><textarea name="content" required style="height: 400px;"></textarea></p>';

				echo '<p class="button"><button type="submit">Publier</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';