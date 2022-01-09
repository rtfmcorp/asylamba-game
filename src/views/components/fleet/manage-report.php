<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

use App\Classes\Library\Format;

$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component report">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a class="more-button" href="' . Format::actionBuilder('deletereport', $sessionToken, ['id' => $report->id]) . '">Supprimer le rapport</a>';

			echo '<a class="more-button" href="' . Format::actionBuilder('archivereport', $sessionToken, ['id' => $report->id]) . '">Archiver le rapport</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
