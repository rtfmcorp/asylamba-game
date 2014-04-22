<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_tech

include_once PROMETHEE;
$technology = new Technology(CTR::$data->get('playerId'));

// session avec les technos de cette base
$S_TQM1 = ASM::$tqm->getCurrentSession();
ASM::$tqm->changeSession($ob_tech->technoQueueManager);
$S_TQM2 = ASM::$tqm->getCurrentSession();

// session avec les technos de toutes les bases
ASM::$tqm->newSession();
ASM::$tqm->load(array('rPlayer' => CTR::$data->get('playerId')));
$S_TQM3 = ASM::$tqm->getCurrentSession();

$S_RSM1 = ASM::$rsm->getCurrentSession();
ASM::$rsm->newSession();
ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

# déblocage
$c1 = ''; $c2 = ''; $c3 = '';
$c4 = ''; $c5 = '';
$c6 = ''; $c7 = '';
for ($i = 0; $i < TQM_TECHNOQUANTITY; $i++) {
	if (!TechnologyResource::isATechnologyNotDisplayed($i)) {
		$but = ''; $sup = ''; $ctn = '';
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
				$sup .= '<em>développement terminé</em>';
				$closed = 'closed';
			} elseif (!TechnologyResource::haveRights($i, 'queue', ASM::$tqm->size())) {
				# queue size
				$but .= '<span class="button disable">';
					$but .= 'file de recherche pleine<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat(TechnologyResource::getInfo($i, 'time', $technology->getTechnology($i) + 1), 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</span>';
			} elseif (!TechnologyResource::haveRights($i, 'credit', $technology->getTechnology($i) + 1, CTR::$data->get('playerInfo')->get('credit'))) {
				# crédit
				$but .= '<span class="button disable">';
					$but .= 'pas assez de crédit<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat(TechnologyResource::getInfo($i, 'time', $technology->getTechnology($i) + 1), 'lite') . '</span> ';
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
					$but .= '<span class="final-time">' . Chronos::secondToFormat(TechnologyResource::getInfo($i, 'time', $technology->getTechnology($i) + 1), 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</span>';
			} else {
				$but .= '<a class="button" href="' . APP_ROOT . 'action/a-buildtechno/baseid-' . $ob_tech->getId() . '/techno-' . $i . '">';
					if (TechnologyResource::isAnUnblockingTechnology($i)) {
						$but .= 'rechercher la technologie<br />';
					} else {
						$but .= 'rechercher le niveau ' . ($technology->getTechnology($i) + 1) . '<br />';
					}
					$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png"> | ';
						$but .= '<span class="final-cost">' . Format::numberFormat(TechnologyResource::getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"> | ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat(TechnologyResource::getInfo($i, 'time', $technology->getTechnology($i) + 1), 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
				$but .= '</a>';
			}
		}

		$ctn .= '<div class="build-item ' . $disability . ' ' . $closed . '">';
			$ctn .= '<div class="name">';
				$ctn .= '<a href="#" class="addInfoPanel hb lt info" title="plus d\'informations" data-techno-id="' . $i . '" data-info-type="techno">+</a>';
				$ctn .= '<img src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($i, 'imageLink') . '.png" alt="" />';
				$ctn .= '<strong>' . $title . '</strong>';
				$ctn .= $sup;
			$ctn .= '</div>';
			$ctn .= '<div class="ship-illu"><img class="illu" src="' . MEDIA . 'technology/img/' . TechnologyResource::getInfo($i, 'imageLink') . '.png" /></div>';
			$ctn .= $but;
		$ctn .= '</div>';

		switch (TechnologyResource::getInfo($i, 'column')) {
			case 1 : $c1 .= $ctn; break;
			case 2 : $c2 .= $ctn; break;
			case 3 : $c3 .= $ctn; break;
			case 4 : $c4 .= $ctn; break;
			case 5 : $c5 .= $ctn; break;
			case 6 : $c6 .= $ctn; break;
			case 7 : $c7 .= $ctn; break;
			default: $c1 .= $ctn; break;
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
			echo '<div class="number-box ' . ((CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED) == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus de vitesse de recherche</span>';
				echo '<span class="value">';
					echo CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED) . ' %';
				echo '</span>';
			echo '</div>';
			ASM::$tqm->changeSession($S_TQM2);
			if (ASM::$tqm->size() > 0) {
				echo '<div class="queue">';
				$realSizeQueue = 0;
				$remainingTotalTime = 0;
				$totalTimeTechno = 0;

				for ($i = 0; $i < ASM::$tqm->size(); $i++) {
					$queue = ASM::$tqm->get($i);
					$realSizeQueue++;
					$totalTimeTechno += TechnologyResource::getInfo($queue->technology, 'time', $queue->targetLevel);
					$remainingTotalTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

					echo '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $remainingTotalTime . '" data-progress-total-time="' . $totalTimeTechno . '">';
						echo '<a href="' . APP_ROOT . 'action/a-dequeuetechno/baseid-' . $ob_tech->getId() . '/techno-' . $queue->technology . '"' . 
							'class="button hb lt" title="annuler la recherche">×</a>';
						echo  '<img class="picto" src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($queue->technology, 'imageLink') . '.png" alt="" />';
						echo '<strong>' . TechnologyResource::getInfo($queue->technology, 'name');
						if (!TechnologyResource::isAnUnblockingTechnology($queue->technology)) {
							echo ' <span class="level">niv. ' . $queue->targetLevel . '</span>';
						}
						echo '</strong>';
						
						if ($realSizeQueue > 1) {
							echo '<em>en attente</em>';
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
				}

				if ($realSizeQueue >= TQM_TECHNOMAXQUEUE) {
					echo '<p><em>file de construction pleine, revenez dans un moment.</em></p>';
				}
				echo '</div>';
			} else {
				echo '<p><em>Aucune technologie en développement !</em></p>';
			}
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
				echo $c4;
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations industrielles II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo $c5;
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
				echo $c6;
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations militaires II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo $c7;
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
			echo $c1;
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis I</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo $c2;
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis II</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo $c3;
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(5, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$tqm->changeSession($S_TQM1);
ASM::$rsm->changeSession($S_RSM1);
?>