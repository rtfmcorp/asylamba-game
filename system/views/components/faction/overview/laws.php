<?php
# createTopic component
# in demeter.forum package

use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Demeter\Resource\LawResources;

# require
$lawManager = $this->getContainer()->get('demeter.law_manager');

echo '<div class="component uni">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Lois actives</h4>';

			$S_LAM_TMP = $lawManager->getCurrentSession();
			$lawManager->changeSession($S_LAM_ACT);

			for ($i = 0; $i < $lawManager->size(); $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo($lawManager->get($i)->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if ($lawManager->size() == 0) {
				echo '<p><em>Aucune loi active</em></p>';
			}

			$lawManager->changeSession($S_LAM_TMP);

			echo '<h4>Lois en cours de votation</h4>';

			$S_LAM_TMP = $lawManager->getCurrentSession();
			$lawManager->changeSession($S_LAM_VOT);

			for ($i = 0; $i < $lawManager->size(); $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo($lawManager->get($i)->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if ($lawManager->size() == 0) {
				echo '<p><em>Aucune loi en cours de votation</em></p>';
			}

			echo '<h4>Bonus de faction</h4>';

			$bonus = $faction->bonusText;
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