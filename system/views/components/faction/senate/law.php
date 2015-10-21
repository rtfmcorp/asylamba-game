<?php
# require
	# LAW/Token 		S_LAM_TOVOTE

$S_LAM_LAW = ASM::$lam->getCurrentSession();
ASM::$lam->changeSession($S_LAM_TOVOTE);

# durée de la loi
$lawDuration = (strtotime($law->dEnd) - strtotime($law->dEndVotation)) / 3600;
$lawDuration = $lawDuration < 1 ? 1 : $lawDuration;

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
					echo '<a class="button" href="' . Format::actionBuilder('votelaw', ['rlaw' => $law->id, 'choice' => '1']) . '" style="text-align: center; line-height: 35px; display: inline-block; width: 104px; margin-right: 0;">';
						echo '<span class="text">Pour</span>';
					echo '</a>';

					echo '<a class="button" href="' . Format::actionBuilder('votelaw', ['rlaw' => $law->id, 'choice' => '0']) . '" style="text-align: center; line-height: 35px; display: inline-block; width: 104px;">';
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
					echo '<span class="value">';
						echo LawResources::getInfo($law->type, 'bonusLaw')
							? Format::number(LawResources::getInfo($law->type, 'price') * $faction->activePlayers * $lawDuration)
							: Format::number(LawResources::getInfo($law->type, 'price'));
					echo '<img class="icon-color" src="http://localhost/asylamba/game/public/media/resources/credit.png" alt="crédits"></span>';
				echo '</li>';

				if (LawResources::getInfo($law->type, 'bonusLaw')) {
					echo '<li>';
						echo '<span class="label">Durée d\'application</span>';
						echo '<span class="value">' . Format::number($lawDuration) . ' relève' . Format::plural($lawDuration) . '</span>';
					echo '</li>';
				} else {
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
			echo '<p>Mise en application ' . Chronos::transform($law->dEndVotation) . '</p>';
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$lam->changeSession($S_LAM_LAW);