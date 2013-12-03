<?php
echo '<h1>Modification de la Galaxie</h1>';
	include_once GAIA;

	$gc = new GalaxyManager($mapGalaxyResource);
	$gc->generateSystem(10);
	echo 'Génération des systems <br/>';
	$gc->generatePlace();
	echo 'Génération des places <br/>';
	$gc->generateSector();
	echo 'Génération des sectors <br/>';
	$gc->associateSystemToSector();
	echo 'Association systems/sectors <br/>';
	$gc->save();
	echo 'Enregistrement <br/>';

	$sm = new SectorManager();
	$sm->load(array());
	$barycentres = array();
	for ($i = 0; $i < $sm->size(); $i++) {
		$strArray = $gc->getCoordPolygon($i, 1, 0);
		$array = explode(', ', $strArray);

		$gx = 0; $gy = 0;
		$vx = 0; $vy = 0;
		$lenght = count($array) / 2;
		for ($j = 0; $j < count($array); $j = $j + 2) {
			$vx += $array[$j];
			$vy += $array[$j + 1];
		}

		$gx = round($vx / $lenght);
		$gy = round($vy / $lenght);

		$db = DataBase::getInstance();
		$qr = $db->query('UPDATE sector
			SET xBarycentric = ' . $gx . ',
				yBarycentric = ' . $gy . '
			WHERE id = ' . ($i + 1));
	}
	echo 'Création des barycentres <br/>';
	echo '------------------------ <br/>';
	echo 'Statistiques \n';
	
	$gc->sectorStatistic();

	$gc->printTimeToStep();
?>