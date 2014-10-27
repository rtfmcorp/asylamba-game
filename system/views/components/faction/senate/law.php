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

				echo '<a class="button" href="' . APP_ROOT . 'action/a-votelaw/rlaw-' . $law->id . '/choice-1">';
					echo '<span class="text">';
						echo 'Voter pour';
					echo '</span>';
				echo '</a>';

				echo '<a class="button" href="' . APP_ROOT . 'action/a-votelaw/rlaw-' . $law->id . '/choice-0">';
					echo '<span class="text">';
						echo 'Voter contre';
					echo '</span>';
				echo '</a>';
			echo '</div>';

			if (!LawResources::getInfo($law->type, 'bonusLaw')) {
				echo '<h4>Modalités d\'application</h4>';
				var_dump($law->options);			
			}

			echo '<h4>Date application</h4>';
			echo '<p>Début dans ' . $law->dEndVotation . '</p>';

			if (LawResources::getInfo($law->type, 'bonusLaw')) {
				echo '<p>Fin dans ' . $law->dEnd . '</p>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';