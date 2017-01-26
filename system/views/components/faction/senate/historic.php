<?php

use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Demeter\Model\Law\Law;

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Historique des votations</h4>';

			foreach ($laws as $law) { 
				echo '<div class="build-item base-type">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
						echo '<strong>' . LawResources::getInfo($law->type, 'name') . '</strong>';
					echo '</div>';

					echo '<p class="desc">';
						if (LawResources::getInfo($law->type, 'department') == 6) {
							echo '<strong>Statut</strong> : appliquée<br>';
						} else {
							echo '<strong>Statut</strong> : ';
							switch ($law->statement) {
								case Law::EFFECTIVE: echo 'application en cours'; break;
								case Law::OBSOLETE: echo 'application terminée'; break;
								case Law::REFUSED: echo 'refusée'; break;
								default: echo 'inconnu'; break;
							}
							echo '<br>';
							echo '<strong>Votes pour</strong> : ' . $law->forVote . '<br>';
							echo '<strong>Votes contre</strong> : ' . $law->againstVote;
						}
					echo '</p>';
					echo '<p>';
						echo Chronos::transform($law->dCreation) . '<br>';
					echo '</p>';
				echo '</div>';
			}

			if (count($laws) === 0) {
				echo '<p><em>Aucune loi n\'a été votée pour le moment.</em></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
