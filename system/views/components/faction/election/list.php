<?php

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');

$S_CAM_1 = $candidateManager->getCurrentSession();
$candidateManager->changeSession($S_CAM_CAN);

$hasIPresented = FALSE;
for ($i = 0; $i < $candidateManager->size(); $i++) { 
	if ($candidateManager->get($i)->rPlayer == $session->get('playerId')) {
		$hasIPresented = TRUE;
		break;
	}
}

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Candidat' . Format::plural($candidateManager->size()) . ' à l\'élection</h4>';

			echo '<div class="set-item">';
				if ($faction->electionStatement == Color::CAMPAIGN && !$hasIPresented) {
					echo '<a class="' . (($request->query->get('candidate') === 'create')  ? 'active' : NULL) . ' item" href="' . APP_ROOT . 'faction/view-election/candidate-create">';
						echo '<div class="left">';
							echo '<span>+</span>';
						echo '</div>';

						echo '<div class="center">';
							echo 'Proposer sa candidature';
						echo '</div>';
					echo '</a>';
				}

				if ($candidateManager->size() > 0) {
					for ($i = 0; $i < $candidateManager->size(); $i++) {
						$candidat = $candidateManager->get($i);
						$status = ColorResource::getInfo($faction->id, 'status');

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img src="' . MEDIA . 'avatar/small/' . $candidat->avatar . '.png" alt="' . $candidat->name . '" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $candidat->name . '</strong>';
								echo $status[$candidat->status - 1];
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (($request->query->get('candidate') === $candidat->id) ? 'active' : NULL) . '" href="' . APP_ROOT . 'faction/view-election/candidate-' . $candidat->id . '"></a>';
							echo '</div>';
						echo '</div>';
					}

					echo '</div>';
				} else {
					echo '</div>';
					echo '<p>Il n\'y a aucun candidat pour l\'instant.</p>';
				}
		echo '</div>';
	echo '</div>';
echo '</div>';

$candidateManager->changeSession($S_CAM_1);