<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_tech

include_once PROMETHEE;
$technology = new Technology(CTR::$data->get('playerId'));

# session avec les technos de cette base
$S_TQM1 = ASM::$tqm->getCurrentSession();
ASM::$tqm->changeSession($ob_tech->technoQueueManager);
$S_TQM2 = ASM::$tqm->getCurrentSession();

# session avec les technos de toutes les bases
ASM::$tqm->newSession();
ASM::$tqm->load(array('rPlayer' => CTR::$data->get('playerId')));
$S_TQM3 = ASM::$tqm->getCurrentSession();

$S_RSM1 = ASM::$rsm->getCurrentSession();
ASM::$rsm->newSession();
ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

# déblocage
$c1 = array(); $c2 = array(); $c3 = array();
$c4 = array(); $c5 = array();
$c6 = array(); $c7 = array();

for ($i = 0; $i < Technology::QUANTITY; $i++) {
	if (!TechnologyResource::isATechnologyNotDisplayed($i)) {
		$but = ''; $sup = ''; $ctn = array(); $ctn[0] = ''; $ctn[1] = TRUE;
		$disability = 'disable'; $closed = '';
		$inQueue = FALSE;
		$inALocalQueue = FALSE;

		ASM::$tqm->changeSession($S_TQM3);
		for ($j = 0; $j < ASM::$tqm->size(); $j++) {
			if (ASM::$tqm->get($j)->technology == $i) {
				$inQueue = TRUE;
				ASM::$tqm->changeSession($S_TQM2);
				for ($k = 0; $k < ASM::$tqm->size(); $k++) {
					if (ASM::$tqm->get($k)->technology == $i) {
						$inALocalQueue = TRUE;
					}
				}
				ASM::$tqm->changeSession($S_TQM3);
			}
		}
		ASM::$tqm->changeSession($S_TQM2);
		
		$title = TechnologyResource::getInfo($i, 'name');
		if (!TechnologyResource::isAnUnblockingTechnology($i) && $technology->getTechnology($i) != 0) {
			$title .= ' [' . $technology->getTechnology($i) . ']';
		}

		if (TechnologyResource::isAnUnblockingTechnology($i)) {
			$answer = TechnologyResource::haveRights($i, 'research', 1, ASM::$rsm->get()->getResearchList());
		} else {
			$answer = TechnologyResource::haveRights($i, 'research', $technology->getTechnology($i) + 1, ASM::$rsm->get()->getResearchList());
		}

		if (!TechnologyResource::haveRights($i, 'technosphereLevel', $ob_tech->getLevelTechnosphere())) {
			# building
			$but  .= '<span class="button disable">';
				$but .= 'il vous faut augmenter votre technosphère au niveau ' . TechnologyResource::getInfo($i, 'requiredTechnosphere');
			$but .= '</span>';
		} elseif ($answer !== TRUE) {
			# recherche
			$but  .= '<span class="button disable">';
				$but .= 'nécessite ';
				for ($j = 0; $j < $answer->size(); $j++) { 
					$but .= $answer->get($j)->get('techno') . '&nbsp;' . $answer->get($j)->get('level');
					if ($j < ($answer->size() - 1)) { $but .= ', '; }
				}
			$but .= '</span>';
		} else {
			# usable techno
			$disability = '';

			# compute time to build with the bonuses
			$timeToBuild = TechnologyResource::getInfo($i, 'time', $technology->getTechnology($i) + 1);
			$bonusPercent = CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
			if (CTR::$data->get('playerInfo')->get('color') == ColorResource::APHERA) {
				# bonus if the player is from Aphera
				$bonusPercent += ColorResource::BONUS_APHERA_TECHNO;
			}
			$timeToBuild -= round($timeToBuild * $bonusPercent / 100);

			if ($inQueue) {
				$but .= '<span class="button disable">';
					$but .= 'technologie en cours<br />de recherche';
					if (!TechnologyResource::isAnUnblockingTechnology($i)) {
						$but .= ' vers le niveau ' . ($technology->getTechnology($i) + 1);
					}
					if (!$inALocalQueue) {
						$but .= ' sur une autre de vos bases';
					}
				$but .= '</span>';

			} elseif (TechnologyResource::isAnUnblockingTechnology($i) && $technology->getTechnology($i)) {
				$ctn[1] = FALSE;
				$sup .= '<em>développement terminé</em>';
				$closed = 'closed';
			} elseif (!TechnologyResource::haveRights($i, 'queue', $ob_tech, ASM::$tqm->size())) {
				# queue size
				$but .= '<span class="button disable">';
					$but .= 'file de recherche pleine<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</span>';
			} elseif (!TechnologyResource::haveRights($i, 'credit', $technology->getTechnology($i) + 1, CTR::$data->get('playerInfo')->get('credit'))) {
				# crédit
				$but .= '<span class="button disable">';
					$but .= 'pas assez de crédits<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</span>';
			} elseif (!TechnologyResource::haveRights($i, 'resource', $technology->getTechnology($i) + 1, $ob_tech->getResourcesStorage())) {
				# ressources
				$but .= '<span class="button disable">';
					$but .= 'pas assez de ressources<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</span>';
			} else {
				$but .= '<a class="button" href="' . Format::actionBuilder('buildtechno', ['baseid' => $ob_tech->getId(), 'techno' => $i]) . '">';
					if (TechnologyResource::isAnUnblockingTechnology($i)) {
						$but .= 'rechercher la technologie<br />';
					} else {
						$but .= 'rechercher le niveau ' . ($technology->getTechnology($i) + 1) . '<br />';
					}
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png"> | ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> | ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</a>';
			}
		}

		$ctn[0] .= '<div class="build-item ' . $disability . ' ' . $closed . '">';
			$ctn[0] .= '<div class="name">';
				$ctn[0] .= '<a href="#" class="addInfoPanel hb lt info" title="plus d\'informations" data-techno-id="' . $i . '" data-info-type="techno">+</a>';
				$ctn[0] .= '<img src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($i, 'imageLink') . '.png" alt="" />';
				$ctn[0] .= '<strong>' . $title . '</strong>';
				$ctn[0] .= $sup;
			$ctn[0] .= '</div>';
			$ctn[0] .= '<div class="ship-illu"><img class="illu" src="' . MEDIA . 'technology/img/' . TechnologyResource::getInfo($i, 'imageLink') . '.png" /></div>';
			$ctn[0] .= $but;
		$ctn[0] .= '</div>';

		switch (TechnologyResource::getInfo($i, 'column')) {
			case 1 : $c1[] = $ctn; break;
			case 2 : $c2[] = $ctn; break;
			case 3 : $c3[] = $ctn; break;
			case 4 : $c4[] = $ctn; break;
			case 5 : $c5[] = $ctn; break;
			case 6 : $c6[] = $ctn; break;
			case 7 : $c7[] = $ctn; break;
			default: $c1[] = $ctn; break;
		}
	}
}

echo '<div class="component techno">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(5, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_tech->getLevelTechnosphere() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$bonus = CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
			if (CTR::$data->get('playerInfo')->get('color') == ColorResource::APHERA) {
				# bonus if the player is from Aphera
				$bonus += ColorResource::BONUS_APHERA_TECHNO;
			}
			echo '<div class="number-box ' . (($bonus == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus de vitesse de recherche</span>';
				echo '<span class="value">';
					echo $bonus . ' %';
				echo '</span>';
			echo '</div>';
			ASM::$tqm->changeSession($S_TQM2);

			echo '<h4>File de construction</h4>';
			echo '<div class="queue">';
				$realSizeQueue = 0;
				$remainingTotalTime = 0;
				$totalTimeTechno = 0;

				for ($i = 0; $i < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $ob_tech->levelTechnosphere, 'nbQueues'); $i++) {
					if (ASM::$tqm->get($i) !== FALSE) {
						$queue = ASM::$tqm->get($i);
						$realSizeQueue++;
						$totalTimeTechno += TechnologyResource::getInfo($queue->technology, 'time', $queue->targetLevel);
						$remainingTotalTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

						echo '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $remainingTotalTime . '" data-progress-total-time="' . $totalTimeTechno . '">';
							echo '<a href="' . Format::actionBuilder('dequeuetechno', ['baseid' => $ob_tech->getId(), 'techno' => $queue->technology]) . '"' . 
								'class="button hb lt" title="annuler la recherche (attention, vous ne récupérerez que ' . TQM_RESOURCERETURN * 100 . '% du montant investi)">×</a>';
							echo  '<img class="picto" src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($queue->technology, 'imageLink') . '.png" alt="" />';
							echo '<strong>' . TechnologyResource::getInfo($queue->technology, 'name');
							if (!TechnologyResource::isAnUnblockingTechnology($queue->technology)) {
								echo ' <span class="level">niv. ' . $queue->targetLevel . '</span>';
							}
							echo '</strong>';
							
							if ($realSizeQueue > 1) {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTotalTime, 'lite') . '</span></em>';
								echo '<span class="progress-container"></span>';
							} else {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTotalTime, 'lite') . '</span></em>';
								echo '<span class="progress-container">';
									echo '<span style="width: ' . Format::percent($totalTimeTechno - $remainingTotalTime, $totalTimeTechno) . '%;" class="progress-bar">';
									echo '</span>';
								echo '</span>';
							}
						echo '</div>';
					} else {
						echo '<div class="item empty">';
							echo '<span class="picto"></span>';
							echo '<strong>Emplacement libre</strong>';
							echo '<span class="progress-container"></span>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

# financial
if (in_array($ob_tech->typeOfBase, array(OrbitalBase::TYP_COMMERCIAL, OrbitalBase::TYP_CAPITAL))) {
	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations industrielles I</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c4 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations industrielles II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c5 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

# military
if (in_array($ob_tech->typeOfBase, array(OrbitalBase::TYP_MILITARY, OrbitalBase::TYP_CAPITAL))) {
	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations militaires I</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c6 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations militaires II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c7 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

# unblock
echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Nouvelles technologies</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c1 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c1 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis I</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c2 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c2 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis II</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c3 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c3 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$tqm->changeSession($S_TQM1);
ASM::$rsm->changeSession($S_RSM1);
?>