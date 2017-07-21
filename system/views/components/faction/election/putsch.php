<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$session = $this->getContainer()->get('session_wrapper');

$follower = 0;
foreach ($votes as $vote) { 
	if ($vote->rCandidate == $candidat->rPlayer) {
		$follower++;
	}
}

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Coup d\'état</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$startPutsch  = $faction->dLastElection;
			$endPutsch    = Utils::addSecondsToDate($faction->dLastElection, Color::PUTSCHTIME);
			$remainPutsch = Utils::interval(Utils::now(), $endPutsch, 's');
			
			echo '<div class="center-box">';
				echo '<span class="label">La tentative de coup d\'état se termine dans</span>';
				echo '<span class="value">' . Chronos::secondToFormat($remainPutsch, 'lite') . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">Nombre de soutiens pour réussir</span>';
				echo '<span class="value">' . $follower . '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent($follower, count($factionPlayers)) . '%;" class="content"></span>';
					echo '<span class="step" style="left: ' . (Color::PUTSCHPERCENTAGE + 2) . '%;">';
						echo '<span class="label bottom">soutiens nécessaires</span>';
					echo '</span>';
				echo '</span>';
					echo '<br />';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo($session->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';