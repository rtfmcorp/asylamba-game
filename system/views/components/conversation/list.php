<?php
echo '<div class="component">';
	echo '<div class="head skin-2"><h2>Messagerie</h2></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="set-item">';
				echo '<a class="item" href="' . APP_ROOT . 'message/conversation-new">';
					echo '<div class="left">';
						echo '<span>+</span>';
					echo '</div>';
					echo '<div class="center">Démarrer une nouvelle conversation</div>';
				echo '</a>';
			echo '</div>';

			echo '<div class="set-conv">';
				for ($i = 0; $i < ASM::$cvm->size(); $i++) {
					$conv = ASM::$cvm->get($i);

					$convAvatar = NULL;
					$convName = array();
					$convColor = 0;
					$counter = 0;
					$restPlayer = 0;

					$ownLastView = NULL;

					foreach ($conv->players as $player) {
						if (CTR::$data->get('playerId') !== $player->rPlayer) {
							if ($counter < 5) {
								$convName[] = '<strong>' . $player->playerName . '</strong>';
							} else {
								$restPlayer++;
							}
							
							if ($counter == 0) {
								$convAvatar = $player->playerAvatar;
								$convColor = $player->playerColor;
							}

							$counter++;
						} else {
							$ownLastView = $player->dLastView;
						}
					}

					if ($restPlayer !== 0) {
						$convName[count($convName) - 1] .= ' et <strong>' . $restPlayer . '+</strong>';
					}

					if ($counter > 2) {
						$convAvatar = 'multi';
						$convColor  = 0;
					}

					echo '<a class="item" href="' . APP_ROOT . 'message/conversation-' . $conv->id . '">';
						echo '<span class="cover">';
							echo '<img src="' . MEDIA . 'avatar/small/' . $convAvatar . '.png" alt="" class="picture color' . $convColor . '" />';
							echo '<span class="number">' . $conv->messages . '</span>';
							if (strtotime($ownLastView) < strtotime($conv->dLastMessage)) {
								echo '<span class="new-message"><img src="' . MEDIA . 'common/nav-message.png" alt="" /></span>';
							}
						echo '</span>';

						echo '<span class="data">';
							echo Chronos::transform($conv->dLastMessage) . '<br />';
							echo empty($conv->title)
								? implode(', ', $convName)
								: '<strong>' . $conv->title . '</strong>';
						echo '</span>';
					echo '</a>';
				}	
			echo '</div>';

			if (ASM::$cvm->size() == 0) {
				echo '<p>Aucune conversation</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';