<?php

use Asylamba\Classes\Library\Format;

# require
	# LAW/Token 		S_LAM_TOVOTE

$lawManager = $this->getContainer()->get('demeter.law_manager');

$S_LAM_LAW = $lawManager->getCurrentSession();
$lawManager->changeSession($S_LAM_TOVOTE);

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h1>Sénat</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">Lois en cours de votation</span>';
				echo '<span class="value">';
					echo Format::number($lawManager->size());
				echo '</span>';
			echo '</div>';

			#echo '<p class="info">Infos sur les lois</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$lawManager->changeSession($S_LAM_LAW);