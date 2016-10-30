<?php

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Gouvernement</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'law') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-law" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Lois</strong>';
				echo '<em>Promulger de nouvelles lois</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'news') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-news" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Annonces</strong>';
				echo '<em>Gestion des annonces</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'message') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-message" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Messages groupés</strong>';
				echo '<em>Envoi de messages aux membres de la faction</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'description') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-description" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Description</strong>';
				echo '<em>Edition de la description publique</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'credit') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-credit" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Gestion des finances</strong>';
				echo '<em>Envoi de crédits aux membres de la faction</em>';
			echo '</a>';

			if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
				$active = (CTR::$get->get('mode') == 'manage') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'faction/view-government/mode-manage" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
					echo '<strong>Gouvernement</strong>';
					echo '<em>Gestion de votre gouvernement</em>';
				echo '</a>';
			}

			echo '<hr />';
			echo '<h4>Abandonner ses fonctions</h4>';

			if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
				if ($faction->regime == Color::DEMOCRATIC) {
					echo '<a href="' . Format::actionBuilder('abdicate') . '" class="more-button confirm" data-confirm-label="Cette action est définitive.">Organiser des élections anticipées</a>';
				} else {
					$S_PAM_DGG2 = ASM::$pam->getCurrentSession();
					ASM::$pam->changeSession($PLAYER_SENATE_TOKEN);

					echo '<form action="' . Format::actionBuilder('abdicate') . '" method="post" class="choose-government">';
						echo '<select name="rplayer">';
							echo '<option value="-1">Choisissez un joueur</option>';
							for ($j = 0; $j < ASM::$pam->size(); $j++) {
								echo '<option value="' . ASM::$pam->get($j)->id . '">' . ColorResource::getInfo(ASM::$pam->get($j)->rColor, 'status')[ASM::$pam->get($j)->status - 1] . ' ' . ASM::$pam->get($j)->name . '</option>';
							}
						echo '</select>';
						echo '<button type="submit">Désigner comme successeur</button>';
					echo '</form>';
					
					ASM::$pam->changeSession($S_PAM_DGG2);
				}
			} else {
				echo '<a href="' . Format::actionBuilder('resign') . '" class="more-button confirm" data-confirm-label="Cette action est définitive.">Démissioner du gouvernement</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';