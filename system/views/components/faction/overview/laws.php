<?php
# createTopic component
# in demeter.forum package

use Asylamba\Modules\Demeter\Resource\LawResources;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');

echo '<div class="component uni">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Lois actives</h4>';

			foreach ($effectiveLaws as $law) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo($law->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if (count($effectiveLaws) === 0) {
				echo '<p><em>Aucune loi active</em></p>';
			}

			echo '<h4>Lois en cours de votation</h4>';

			foreach ($votingLaws as $law) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo($law->type, 'name') . '</strong>';
					echo '</div>';
				echo '</div>';
			}

			if (count($votingLaws) === 0) {
				echo '<p><em>Aucune loi en cours de votation</em></p>';
			}

			echo '<h4>Bonus de faction</h4>';

			$bonus = $faction->bonusText;
			foreach ($bonus as $b) {
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . $mediaPath . $b['path'] . '" alt="" />';
						echo '<strong>' . $b['title'] . '</strong>';
						echo '<em>' . $b['desc'] . '</em>';
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
