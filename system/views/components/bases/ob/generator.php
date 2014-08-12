<?php
# generator component
# in athena.bases package

# affichage du générateur

# require
	# {orbitalBase}		ob_generator

# work
$q = '';
$b = array('', '', '', '', '', '', '', '');
$realSizeQueue = 0;
for ($i = 0; $i < 8; $i++) {
	$name 		= ucfirst(OrbitalBaseResource::getBuildingInfo($i, 'name'));
	$aLevel[$i] = intval(call_user_func(array($ob_generator, 'getLevel' . $name)));
	$rLevel[$i] = intval(call_user_func(array($ob_generator, 'getReal' . $name . 'Level')));
}

# queue
$S_BQM1 = ASM::$bqm->getCurrentSession();
ASM::$bqm->changeSession($ob_generator->buildingManager);

if (ASM::$bqm->size() != 0) {
	$q .= '<div class="queue">';
	$nextTime = 0;
	$nextTotalTime = 0;

	for ($i = 0; $i < ASM::$bqm->size(); $i++) {
		$qe = ASM::$bqm->get($i);

		$realSizeQueue++;
		$nextTime = Utils::interval(Utils::now(), $qe->dEnd, 's');
		$nextTotalTime += OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time');

		$q .= '<div class="item ' . (($realSizeQueue > 1) ? 'active' : '') . ' progress" data-progress-output="lite" data-progress-current-time="' . $nextTime . '" data-progress-total-time="' . $nextTotalTime . '">';
		$q .= '<a href="' . APP_ROOT . 'action/a-dequeuebuilding/baseid-' . $ob_generator->getId() . '/building-' . $qe->buildingNumber . '"' . 
				'class="button hb lt" title="annuler la construction">×</a>';
		$q .= '<img class="picto" src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
		$q .= '<strong>';
			$q .= OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'frenchName');
			$q .= ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
		$q .= '</strong>';
		if ($realSizeQueue > 1) {
			$q .= '<em>en attente</em>';
			$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';
			$q .= '<span class="progress-container"></span>';
		} else {
			$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';

			$q .= '<span class="progress-container">';
				$q .= '<span style="width: ' . Format::percent($nextTotalTime - $nextTime, $nextTotalTime) . '%;" class="progress-bar">';
				$q .='</span>';
			$q .= '</span>';
		}
		$q .= '</div>';		
	}
	if ($realSizeQueue >= BQM_MAXQUEUE) {
		$q .= '<p><em>file de construction pleine...</em></p>';
	}
	$q .= '</div>';
} else {
	$q .= '<p><em>Aucun bâtiment en construction !</em></p>';
}
ASM::$bqm->changeSession($S_BQM1);


# building
$technology = new Technology(CTR::$data->get('playerId'));
for ($i = 0; $i < 8; $i++) {
	$level = $aLevel[$i];
	$nextLevel =  $rLevel[$i] + 1;

	$b[$i] .= ($rLevel[$i]) ? '<div class="build-item">' : '<div class="build-item disable">';
		$b[$i] .= '<div class="name">';
			$b[$i] .= '<img src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($i, 'imageLink') . '.png" alt="" />';
			$b[$i] .= '<strong>' . OrbitalBaseResource::getBuildingInfo($i, 'frenchName') . '</strong>';
			if ($level != 0) {
				$b[$i] .= '<span class="level hb lt" title="niveau actuel">' . $level . '</span>';
			}
			$b[$i] .= '<a href="#" class="addInfoPanel info hb lt" title="plus d\'info" data-building-id="' . $i . '" data-info-type="building" data-building-current-level="' . $level . '">+</a>';
		$b[$i] .= '</div>';

		$price = Format::numberFormat(OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice'), -1) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressources" class="icon-color" />';
		$time  = Chronos::secondToFormat(OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'time'), 'lite') . ' <img src="' .  MEDIA. 'resources/time.png" alt="relèves" class="icon-color" />';

		if (($answer = OrbitalBaseResource::haveRights($i, $nextLevel, 'buildingTree', $ob_generator)) !== TRUE) {
			if ($answer == 'niveau maximum atteint') {
				$b[$i] .= '<span class="button disable">';
					$b[$i] .= '<span class="text">';
						$b[$i] .= 'construction impossible<br/>';
						$b[$i] .= 'niveau maximum atteint';
					$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			} else {
				$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
					$b[$i] .= '<span class="text">';
						$b[$i] .= 'construction impossible<br/>';
						$b[$i] .= $price . ' | ' . $time;
					$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			}
		} elseif (($answer = OrbitalBaseResource::haveRights($i, $nextLevel, 'techno', $technology)) !== TRUE) {
			$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} elseif (!OrbitalBaseResource::haveRights($i, $nextLevel, 'queue', $realSizeQueue)) {
			$b[$i] .= '<span class="button disable hb lt" title="file de construction pleine, revenez dans un moment">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} elseif (!OrbitalBaseResource::haveRights($i, $nextLevel, 'resource', $ob_generator->getResourcesStorage())) {
			$b[$i] .= '<span class="button disable hb lt" title="pas assez de ressources, il manque ' . Format::numberFormat(OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice') - $ob_generator->getResourcesStorage()) . ' ressources">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} else {
			$b[$i] .= '<a class="button" href="' . APP_ROOT . 'action/a-buildbuilding/baseid-' . $ob_generator->getId() . '/building-' . $i . '">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'augmenter vers le niveau ' . $nextLevel . '<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</a>';
		}
	$b[$i] .= '</div>';
}

# display
echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(0, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_generator->getLevelGenerator() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box ' . ((CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED) == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus de vitesse de production</span>';
				echo '<span class="value">';
					echo Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED)) . ' %';
				echo '</span>';
			echo '</div>';
			
			echo $q;
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component size2 generator">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<table>';
				echo '<tr>';
					echo '<td>' . $b[0] . '</td>';
					echo '<td>' . $b[5] . '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>' . $b[1] . '</td>';
					echo '<td>' . $b[2] . '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>' . $b[6] . '</td>';
					echo '<td>' . $b[3] . '</td>';
				echo '</tr>';
				/*echo '<tr>';
					echo '<td>' . $b[4] . '</td>';
					echo '<td>' . $b[7] . '</td>';
				echo '</tr>';*/
			echo '</table>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(0, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>