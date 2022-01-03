<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);

echo '<div class="component new-message">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('updatefactiondesc', $sessionToken) . '" method="POST" />';
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
			echo '<p class="long-info text-bloc">' . $colorManager->getParsedDescription($faction) . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
