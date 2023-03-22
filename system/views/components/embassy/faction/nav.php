<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Ambassades</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($factions as $otherFaction) {
				$id = $otherFaction->id;
				$active = $faction->id == $id ? 'active' : NULL;

				echo '<a href="' . $appRoot . 'embassy/faction-' . $id . '" class="nav-element ' . $active . '">';
					echo '<img src="' . $mediaPath . 'avatar/small/color-' . $id . '.png" alt="" />';
					echo '<strong>' . ColorResource::getInfo($id, 'officialName') . '</strong>';
					echo '<em>Ambassade des ' . ucfirst(ColorResource::getInfo($id, 'demonym')) . '</em>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
