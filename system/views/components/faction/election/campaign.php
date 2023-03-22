<?php
# require
use Asylamba\Modules\Demeter\Resource\ColorResource;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Campagne</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">nombre de candidats</span>';
				echo '<span class="value">' . $nbCandidate . '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo($session->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
