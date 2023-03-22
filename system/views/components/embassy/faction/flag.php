<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="value">' . ColorResource::getInfo($faction->id, 'officialName') . '</span>';
				echo '<span class="label">' . ColorResource::getInfo($faction->id, 'devise') . '</span>';
			echo '</div>';

			echo '<div class="profil-flag color-' . $faction->id . '"><img src="' . $mediaPath . 'ally/big/color' . $faction->id . '.png" alt=""></div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
