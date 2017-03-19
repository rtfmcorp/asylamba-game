<?php

$sectorManager = $this->getContainer()->get('gaia.sector_manager');

$S_SEM_T = $sectorManager->getCurrentSession();
$sectorManager->newSession();
$sectorManager->load(array('rColor' => $faction->id));

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Imposition</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Impôts courants</h4>';

			echo '<ul class="list-type-1">';
				for ($i = 0; $i < $sectorManager->size(); $i++) {
					$sector = $sectorManager->get($i);

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

$sectorManager->changeSession($S_SEM_T);
