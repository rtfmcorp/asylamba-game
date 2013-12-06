<?php
echo '<div id="systems">';
	$db = DataBase::getInstance();
	$qr = $db->prepare('SELECT * FROM system');
	$qr->execute();
	$aw = $qr->fetchAll();

	# own bases
	$basesId = array();
	for ($i = 0; $i < ASM::$obm->size(); $i++) { 
		$basesId[]  = ASM::$obm->get($i)->getSystem();
	}

	foreach ($aw as $system) {
		$owner = (in_array($system['id'], $basesId)) ? 'class="own"' : '';
		echo '<a ';
			echo 'href="#" ';
			echo 'class="loadSystem" ';
			echo 'data-system-id="' . $system['id'] . '" ';
			echo 'data-x-position="' . $system['xPosition'] . '" data-y-position="' . $system['yPosition'] . '" ';
			echo 'style="top: ' . ($system['yPosition'] * 20 - 10) . 'px; left: ' . ($system['xPosition'] * 20 - 10) . 'px">';
			echo '<img src="' . MEDIA . 'map/systems/t' . $system['typeOfSystem'] . 'c' . $system['rColor'] . '.png" ' . $owner . ' />';
		echo '</a>';
	}

	/*
	A MODIFIER
	##########
	
	for ($i = 0; $i < $sm->size(); $i++) {
		echo '<div class="info color' . $sm->get($i)->getRColor() . '" style="left: ' . $sm->get($i)->getXBarycentric() * 20 . 'px; top: ' . $sm->get($i)->getYBarycentric() * 20 . 'px">';
			$sec = array('Antalès', 'Dermaton', 'Ecaliope', 'Gahal Talmek');
			echo '<h2><span>' . ($i + 1) . '</span> Secteur ' . $sec[rand(0, count($sec) - 1)] . '</h2>';
			echo '<p><a href="#">+</a> ';
			if ($sm->get($i)->getRColor() != 0) {
				echo 'Revendiqué par ' . ColorResource::getInfo($sm->get($i)->getRColor(), 'popularName') . ' | ' . $sm->get($i)->getTax() . '% de taxe';
			} else {
				echo 'Non revendiqué | Aucune taxe</p> ';
			}
			echo '</p>';
		echo '</div>';
	}
	*/
echo '</div>';
?>