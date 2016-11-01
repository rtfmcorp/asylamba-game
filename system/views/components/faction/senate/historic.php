<?php

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Demeter\Model\Law\Law;

# require
	# LAM/Token 		S_LAM_HISTORIC

$S_LAM_LAW = ASM::$lam->getCurrentSession();
ASM::$lam->changeSession($S_LAM_HISTORIC);

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Historique des votations</h4>';

			for ($i = 0; $i < ASM::$lam->size(); $i++) { 
				$law = ASM::$lam->get($i);

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

			if (ASM::$lam->size() == 0) {
				echo '<p><em>Aucune loi n\'a été votée pour le moment.</em></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$lam->changeSession($S_LAM_LAW);