<?php
echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Object de la votation</h4>';

			echo '<div class="build-item base-type">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
					echo '<strong>' . LawResources::getInfo($law->type, 'name') . '</strong>';
				echo '</div>';

				echo '<p class="desc">' . LawResources::getInfo($law->type, 'shortDescription') . '</p>';

				$hasVoted = FALSE;
				for ($j = 0; $j < ASM::$vlm->size(); $j++) { 
					if (ASM::$vlm->get($j)->rPlayer == CTR::$data->get('playerId')) {
						$hasVoted = TRUE;
						break;
					}
				}

				if ($hasVoted) {
					echo '<span class="button disable" style="text-align: center; line-height: 35px;>';
						echo '<span class="text">Vous avez déjà voté</span>';
					echo '</span>';
				} elseif (CTR::$data->get('playerInfo')->get('status') == PAM_PARLIAMENT) {
					echo '<a class="button" href="' . APP_ROOT . 'action/a-votelaw/rlaw-' . $law->id . '/choice-1" style="text-align: center; line-height: 35px; display: inline-block; width: 104px; margin-right: 0;">';
						echo '<span class="text">Pour</span>';
					echo '</a>';

					echo '<a class="button" href="' . APP_ROOT . 'action/a-votelaw/rlaw-' . $law->id . '/choice-0" style="text-align: center; line-height: 35px; display: inline-block; width: 104px;">';
						echo '<span class="text">Contre</span>';
					echo '</a>';
				} else {
					echo '<span class="button disable" style="text-align: center; line-height: 35px;>';
						echo '<span class="text">Seuls les sénateurs peuvent voter</span>';
					echo '</span>';
				}
			echo '</div>';

			echo '<h4>Modalités d\'application</h4>';

			echo '<ul class="list-type-1">';
				echo '<li>';
					echo '<span class="label">Coût</span>';
					echo '<span class="value">' . Format::number(LawResources::getInfo($law->type, 'price')) . '</span>';
				echo '</li>';

				if (!LawResources::getInfo($law->type, 'bonusLaw')) {
					if (isset($law->options['display'])) {
						foreach ($law->options['display'] as $label => $value) {
							echo '<li>';
								echo '<span class="label">' . $label . '</span>';
								echo '<span class="value">' . $value . '</span>';
							echo '</li>';
						}
					}
				}
			echo '</ul>';

			echo '<h4>Date application</h4>';
			if (LawResources::getInfo($law->type, 'bonusLaw')) {
				echo '<p>Début ' . Chronos::transform($law->dEndVotation) . '</p>';
				echo '<p>Fin ' . Chronos::transform($law->dEnd) . '</p>';
			} else {
				echo '<p>Mise en application ' . Chronos::transform($law->dEndVotation) . '</p>';
			}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';