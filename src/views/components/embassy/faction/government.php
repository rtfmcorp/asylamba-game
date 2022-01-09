<?php

use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Library\Format;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Gouvernement</h4>';
			foreach ($governmentMembers as $minister) { 
				echo '<div class="player color' . $faction->id . '">';
					echo '<a href="' . $appRoot . 'embassy/player-' .  $minister->id . '">';
						echo '<img src="' . $mediaPath . 'avatar/small/' .  $minister->avatar . '.png" alt="' .  $minister->name . '" class="picto" />';
					echo '</a>';
					echo '<span class="title">' . $status[ $minister->status - 1] . '</span>';
					echo '<strong class="name">' .  $minister->name . '</strong>';
				echo '</div>';
			}

			if (count($governmentMembers) === 0) {
				echo '<p>Aucun gouvernement formé</p>';
			}

			echo '<h4>Informations</h4>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre de points de la faction</span>';
				echo '<span class="value">' . Format::number($faction->points) . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Richesse de la faction</span>';
				echo '<span class="value">' . Format::number($faction->credits) . ' <img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" /></span>';
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
