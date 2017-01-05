<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Format;

$playerManager = $this->getContainer()->get('zeus.player_manager');

$S_PAM_DGG = $playerManager->getCurrentSession();
$playerManager->changeSession($FACTION_GOV_TOKEN);

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Gouvernement</h4>';
			for ($i = 0; $i < $playerManager->size(); $i++) { 
				echo '<div class="player color' . $faction->id . '">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' .  $playerManager->get($i)->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' .  $playerManager->get($i)->avatar . '.png" alt="' .  $playerManager->get($i)->name . '" class="picto" />';
					echo '</a>';
					echo '<span class="title">' . $status[ $playerManager->get($i)->status - 1] . '</span>';
					echo '<strong class="name">' .  $playerManager->get($i)->name . '</strong>';
				echo '</div>';
			}

			if ($playerManager->size() == 0) {
				echo '<p>Aucun gouvernement formé</p>';
			}

			echo '<h4>Informations</h4>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre de points de la faction</span>';
				echo '<span class="value">' . Format::number($faction->points) . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Richesse de la faction</span>';
				echo '<span class="value">' . Format::number($faction->credits) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" /></span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre de points des territoires contrôlés</span>';
				echo '<span class="value">' . Format::number($faction->sectors) . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre de joueurs actifs</span>';
				echo '<span class="value">' . Format::number($faction->activePlayers) . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$playerManager->changeSession($S_PAM_DGG);