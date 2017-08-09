<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$redisManager = $this->getContainer()->get('redis_manager');

$sectors = $sectorManager->getAll();
$factions = $colorManager->getAll();

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Territoires</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach (['Secteurs conquis', 'Secteurs en balance'] as $type) {
				$displayed = 0;

				echo '<h4>' . $type . '</h4>';
				echo '<ul class="list-type-1">';

				foreach ($sectors as $key => $sector) {
                    $treated = false;
					$percents = ['color' . $faction->getId() => 0];
                    $scores = unserialize($redisManager->getConnection()->get('sector:' . $sector->getId()));

                    if (!isset($scores[$faction->getId()]) && $sector->getRColor() !== $faction->getId()) {
                        unset($sectors[$key]);
                        continue;
                    }
                    if ($type === 'Secteurs conquis' && $sector->getRColor() !== $faction->getId()) {
                        continue;
                    }
					
					foreach ($factions as $f) {
						if ($f->id === 0 || !isset($scores[$f->id])) {
							continue;
						}
						$percents['color' . $f->id] = round(Format::percent($scores[$f->id], array_sum($scores), false));
					}

					arsort($percents);

					if ($sector->getRColor() == $faction->getId() || ($scores[$faction->getId()] > 0)) {
						echo '<li>';
							echo '<a href="#" class="picto color' . $sector->getRColor() . '">' . $sector->getId() . '</a>';
							echo '<span class="label">' .
                                    $sector->getName() . 
                                    ' (' . $sector->getPoints() . ' point' . Format::plural($sector->getPoints()). ')' .
                                '</span>';
                            foreach ($scores as $factionId => $points) {
                                if ($points === 0) {
                                    continue;
                                }
                                echo '<span class="label color' . $factionId . '">' .
                                    ColorResource::getInfo($factionId, 'popularName') . ' : ' . $points . ' point' . Format::plural($points) . ' de contrôle' .
                                 '</span>';
                            }
							echo '<span class="value">' . $percents['color' . $faction->getId()] . ' %</span>';
							echo '<span class="progress-bar hb bl" title="partage des systèmes entre les factions">';
							foreach ($percents as $color => $percent) {
								echo '<span style="width:' . $percent . '%;" class="content ' . $color . '"></span>';
							}
							echo '</span>';
						echo '</li>';

						$displayed++;
                        $treated = true;
					}
                    // If the sector has been displayed, we remove it for the second loop round
                    if ($treated === true) {
                        unset($sectors[$key]);
                    }
				}

				echo '</ul>';
				
				if ($displayed == 0) {
					echo '<p>Aucun secteur</p>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
