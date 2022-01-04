<?php

$sectors = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\SectorManager::class)->getFactionSectors($faction->id);

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Imposition</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Impôts courants</h4>';

			echo '<ul class="list-type-1">';
				foreach ($sectors as $sector) {
					echo '<li>';
						echo '<a href="#" class="picto color' . $sector->rColor . '">' . $sector->id . '</a>';
						echo '<span class="label">' . $sector->name . '</span>';
						echo '<span class="value">' . $sector->tax . ' %</span>';
					echo '</li>';
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';
