<?php
# require

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$voteManager = $this->getContainer()->get('demeter.vote_manager');
$electionManager = $this->getContainer()->get('demeter.election_manager');
$session = $this->getContainer()->get('app.session');

$S_ELM_ELC = $electionManager->getCurrentSession();
$electionManager->changeSession($ELM_ELECTION_TOKEN);

$S_VOM_ELC = $voteManager->getCurrentSession();
$voteManager->changeSession($VOM_ELC_TOTAL_TOKEN);

$nbFactionPlayers = count($factionPlayers);

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Election</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">nombre de candidats</span>';
				echo '<span class="value">' . $nbCandidate . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">taux de participation</span>';
				echo '<span class="value">' . Format::percent($voteManager->size(), $nbFactionPlayers) . ' %</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent($voteManager->size(), $nbFactionPlayers) . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo($session->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$voteManager->changeSession($S_VOM_ELC);
$electionManager->changeSession($S_ELM_ELC);