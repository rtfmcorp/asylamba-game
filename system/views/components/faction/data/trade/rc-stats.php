<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);

$join = 'FROM commercialRoute AS cr
LEFT JOIN orbitalBase AS ob1
ON cr.rOrbitalBase = ob1.rPlace
	LEFT JOIN player AS pl1
	ON ob1.rPlayer = pl1.id
LEFT JOIN orbitalBase AS ob2
ON cr.rOrbitalBaseLinked = ob2.rPlace
	LEFT JOIN player AS pl2
	ON ob2.rPlayer = pl2.id';

$qr = $database->prepare('SELECT
		COUNT(cr.id) AS nb,
		SUM(cr.income) AS income
		' . $join . '
	WHERE (pl1.rColor = ? OR pl2.rColor = ?)
		AND cr.statement = ?
');
$qr->execute(array($faction->id, $faction->id, CommercialRoute::ACTIVE));
$aw1 = $qr->fetch(); $qr->closeCursor();

$qr = $database->prepare('SELECT COUNT(cr.id) AS nb ' . $join . ' WHERE pl1.rColor = ? AND pl2.rColor = ? AND cr.statement = ?');
$qr->execute(array($faction->id, $faction->id, CommercialRoute::ACTIVE));
$aw2 = $qr->fetch(); $qr->closeCursor();

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Commerce</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Routes commerciales</h4>';

			echo '<div class="number-box">';
				echo '<span class="label">routes commerciales actives</span>';
				echo '<span class="value">' . Format::number($aw1['nb']) . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">revenu total par relève</span>';
				echo '<span class="value">';
					echo Format::number($aw1['income']);
					echo ' <img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits">';
				echo '</span>';
				echo '<span class="group-link"><a href="#" title="revenu total encaissé par les joueurs de la faction" class="hb lt">?</a></span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">part du commerce intérieur</span>';
				echo '<span class="value">' . Format::percent($aw2['nb'], $aw1['nb']) . ' %</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent($aw2['nb'], $aw1['nb']) . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<h4>Répartition du commerce extérieur</h4>';

			foreach ($faction->colorLink as $i => $k) {
				if ($i != 0 && $i != $faction->id) {
					$qr = $database->prepare('SELECT
						COUNT(cr.id) AS nb ' . $join . '
						WHERE ((pl1.rColor = ? AND pl2.rColor = ?) OR (pl1.rColor = ? AND pl2.rColor = ?)) AND cr.statement = ?'
					);
					$qr->execute(array($faction->id, ColorResource::getInfo($i, 'id'), ColorResource::getInfo($i, 'id'), $faction->id, CommercialRoute::ACTIVE));
					$aw3 = $qr->fetch(); $qr->closeCursor();

					echo '<div class="number-box grey">';
						echo '<span class="label">routes commerciales avec ' . ColorResource::getInfo($i, 'popularName') . '</span>';
						echo '<span class="value">' . Format::number($aw3['nb']) . '</span>';
						echo '<span class="progress-bar">';
							echo '<span style="width:' . Format::percent($aw3['nb'], (100 - Format::percent($aw2['nb'], $aw1['nb'])) * $aw1['nb'] / 100) . '%;" class="content"></span>';
						echo '</span>';
					echo '</div>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
