<?php
# createTopic component
# in demeter.forum package

# création d'un topic

# require

echo '<div class="component uni">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Lois actives</h4>';

			$S_LAM_TMP = ASM::$lam->getCurrentSession();
			ASM::$lam->changeSession($S_LAM_ACT);

			for ($i = 0; $i < ASM::$lam->size(); $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo(ASM::$lam->get($i)->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if (ASM::$lam->size() == 0) {
				echo '<p><em>Aucune loi active</em></p>';
			}

			ASM::$lam->changeSession($S_LAM_TMP);

			echo '<h4>Lois en cours de votation</h4>';

			$S_LAM_TMP = ASM::$lam->getCurrentSession();
			ASM::$lam->changeSession($S_LAM_VOT);

			for ($i = 0; $i < ASM::$lam->size(); $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo(ASM::$lam->get($i)->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if (ASM::$lam->size() == 0) {
				echo '<p><em>Aucune loi active</em></p>';
			}

			echo '<h4>Bonus de factions</h4>';

			$bonus = ColorResource::getInfo($faction->id, 'bonus');
			foreach ($bonus as $b) {
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . $b['path'] . '" alt="" />';
						echo '<strong>' . $b['title'] . '</strong>';
						echo '<em>' . $b['desc'] . '</em>';
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';