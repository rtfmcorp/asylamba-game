<?php
$S_PAM_DGG = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($FACTION_GOV_TOKEN);

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Gouvernement</h4>';
			for ($i = 0; $i < ASM::$pam->size(); $i++) { 
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' .  ASM::$pam->get($i)->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' .  ASM::$pam->get($i)->avatar . '.png" alt="' .  ASM::$pam->get($i)->name . '" class="picto" />';
					echo '</a>';
					echo '<span class="title">' . $status[ ASM::$pam->get($i)->status - 1] . '</span>';
					echo '<strong class="name">' .  ASM::$pam->get($i)->name . '</strong>';
				echo '</div>';
			}

			if (ASM::$pam->size() == 0) {
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
				echo '<span class="label">Nombre de territoires contrôlés</span>';
				echo '<span class="value">' . Format::number($faction->sectors) . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre de joueurs actifs</span>';
				echo '<span class="value">' . Format::number($faction->activePlayers) . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);