<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$parser = $this->getContainer()->get('parser');
$sessionToken = $this->getContainer()->get('app.session')->get('token');

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player profil size1">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($faction->electionStatement == Color::ELECTION) {
				$hasVoted = FALSE;
				if ($playerVote !== null) {
					$hasVoted = TRUE;
				}

				if ($faction->regime != Color::ROYALISTIC) {
					echo '<div class="build-item">';
						if ($hasVoted) {
							if ($playerVote->rCandidate == $candidat->rPlayer) {
								echo '<span class="button disable" style="text-align: center;">';
									echo '<span class="text" style="line-height: 35px;">Vous avez voté pour lui</span>';
								echo '</span>';
							} else {
								echo '<span class="button disable" style="text-align: center;">';
									echo '<span class="text" style="line-height: 35px;">---</span>';
								echo '</span>';
							}
						} else {
							echo '<a class="button" href="' . Format::actionBuilder('vote', $sessionToken, ['relection' => $rElection, 'rcandidate' => $candidat->rPlayer]) . '" style="text-align: center;">';
								echo '<span class="text" style="line-height: 35px;">Voter</span>';
							echo '</a>';
						}
					echo '</div>';
				} else {					
					echo '<div class="build-item">';
						if ($hasVoted) {
							if ($playerVote->rCandidate == $candidat->rPlayer) {
								echo '<span class="button disable" style="text-align: center;">';
									echo '<span class="text" style="line-height: 35px;">Vous avez soutenu le coup d\'état</span>';
								echo '</span>';
							} else {
								echo '<span class="button disable" style="text-align: center;">';
									echo '<span class="text" style="line-height: 35px;">Vous n\'avez pas soutenu le coup d\'état</span>';
								echo '</span>';
							}
						} else {
							echo '<a class="button" href="' . Format::actionBuilder('vote', $sessionToken,  ['relection' => $rElection, 'rcandidate' => $candidat->rPlayer]) . '" style="text-align: center;">';
								echo '<span class="text" style="line-height: 35px;">Soutenir le coup d\'état</span>';
							echo '</a>';
							echo '<a class="button" href="' . Format::actionBuilder('vote', $sessionToken, ['relection' => $rElection, 'rcandidate' => 0]) . '" style="text-align: center;">';
								echo '<span class="text" style="line-height: 35px;">Rejeter le coup d\'état</span>';
							echo '</a>';
						}
					echo '</div>';
				}
			}

			echo '<div class="center-box">';
				echo '<span class="label">';
					echo $status[$candidat->status - 1];
				echo '</span>';
				echo '<span class="value">' . $candidat->name . '</span>';
			echo '</div>';

			echo '<div class="profil-flag">';
				echo '<img src="' . MEDIA . 'avatar/big/' . $candidat->avatar . '.png" alt="' . $candidat->name . '">';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Son programme</h4>';
			echo '<p>' . $parser->parse($candidat->program) .'</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
