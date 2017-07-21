<?php
# require

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$session = $this->getContainer()->get('session_wrapper');

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
				echo '<span class="value">' . Format::percent(count($votes), $nbFactionPlayers) . ' %</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent(count($votes), $nbFactionPlayers) . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo($session->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
