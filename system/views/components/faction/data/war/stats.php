<?php
include_once ARES;

$db = DataBase::getInstance();

$qr = $db->prepare('SELECT
		COUNT(c.id) AS nb,
		AVG(c.level) AS avgLevel
	FROM commander AS c
		LEFT JOIN player AS p
		ON c.rPlayer = p.id
	WHERE p.rColor = ? AND (c.statement = ? OR c.statement = ?)
');
$qr->execute(array($faction->id, Commander::AFFECTED, Commander::MOVING));
$aw1 = $qr->fetch(); $qr->closeCursor();

$qr = $db->prepare('SELECT
		SUM(s.ship0) AS nbs0,
		SUM(s.ship1) AS nbs1,
		SUM(s.ship2) AS nbs2,
		SUM(s.ship3) AS nbs3,
		SUM(s.ship4) AS nbs4,
		SUM(s.ship5) AS nbs5,
		SUM(s.ship6) AS nbs6,
		SUM(s.ship7) AS nbs7,
		SUM(s.ship8) AS nbs8,
		SUM(s.ship9) AS nbs9,
		SUM(s.ship10) AS nbs10,
		SUM(s.ship11) AS nbs11
	FROM squadron AS s
		LEFT JOIN commander AS c
		ON s.rCommander = c.id
		LEFT JOIN player AS p
		ON c.rPlayer = p.id
	WHERE p.rColor = ? AND (c.statement = ? OR c.statement = ?)
');
$qr->execute(array($faction->id, Commander::AFFECTED, Commander::MOVING));
$aw2 = $qr->fetch(); $qr->closeCursor();

$totalPEV = 0;
for ($i = 0; $i < 12; $i++) {
	$totalPEV += $aw2['nbs' . $i] * ShipResource::getInfo($i, 'pev');
}

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Etats des armées</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Statistiques générales</h4>';

			echo '<div class="number-box">';
				echo '<span class="label">Officiers actifs</span>';
				echo '<span class="value">' . Format::number($aw1['nb']) . '</span>';
				echo '<span class="group-link"><a href="#" title="officiers affectés en première ou deuxième ligne sur des planètes de la faction" class="hb lt">?</a></span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">Grade moyen des officiers actifs</span>';
				echo '<span class="value">' . CommanderResources::getInfo(Format::number($aw1['avgLevel']), 'grade') . '</span>';

			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">PEV totaux</span>';
				echo '<span class="value">';
					echo Format::number($totalPEV);
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/pev.png" alt="pev">';
				echo '</span>';
				echo '<span class="group-link"><a href="#" title="PEV affectés à des commandants actifs" class="hb lt">?</a></span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">PEV moyen par officiers</span>';
				echo '<span class="value">';
					echo Format::number($totalPEV / $aw1['nb']);
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/pev.png" alt="pev">';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<h4>Nombre d\'appareil par type</h4>';
			echo '<ul class="list-type-1">';
			for ($i = 0; $i < 12; $i++) {
				echo '<li>';
					echo '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($i, 'imageLink') . '.png" alt="" />';
					echo '<span class="label">' . ShipResource::getInfo($i, 'name') . ' &laquo;' . ShipResource::getInfo($i, 'codeName') . '&raquo;</span>';
					echo '<span class="value">' . Format::number($aw2['nbs' . $i]) . '</span>';
				echo '</li>';
			}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>