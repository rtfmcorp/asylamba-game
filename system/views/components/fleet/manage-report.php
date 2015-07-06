<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

echo '<div class="component report">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a class="more-button" href="' . Format::actionBuilder('deletereport', ['id' => $report_report->id]) . '">Supprimer le rapport</a>';

			echo '<a class="more-button" href="' . Format::actionBuilder('archivereport', ['id' => $report_report->id]) . '">Archiver le rapport</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';