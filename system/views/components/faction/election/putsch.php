<?php
echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Coup d\'état</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="label">Fin de la tentative de coup d\'état</span>';
				echo '<span class="value">' . Chronos::secondToFormat($remainPutsch, 'lite') . '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>