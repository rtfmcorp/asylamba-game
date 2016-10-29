<?php

use Asylamba\Classes\Database\Database;

$db = Database::getInstance();

$qr = $db->prepare('SELECT
		COUNT(c.id) AS nb
	FROM commander AS c
		LEFT JOIN player AS p
		ON c.rPlayer = p.id
		LEFT JOIN place AS o
		ON c.rDestinationPlace = o.id
	WHERE p.rColor = ? AND c.statement = ? AND o.rPlayer != 0
');
$qr->execute(array($faction->id, Commander::MOVING));
$aw1 = $qr->fetch(); $qr->closeCursor();

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Attaques entrantes</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Opérations en cours</h4>';

			echo '<div class="number-box">';
				echo '<span class="label">Attaques entrantes</span>';
				echo '<span class="value">??</span>';
				echo '<span class="group-link"><a href="#" title="attaques entrantes dont la flotte a déjà pénétré le cercle de contre-espionnage" class="hb lt">?</a></span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">Attaques sortantes</span>';
				echo '<span class="value">' . $aw1['nb'] . '</span>';
				echo '<span class="group-link"><a href="#" title="attaques sortantes des membres de la faction (excepté attaques sur planètes rebelles)" class="hb lt">?</a></span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
